<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupService
{
    public function create(): string
    {
        $directory = (string) config('backup.path');

        File::ensureDirectoryExists($directory);

        $path = $directory.'/backup-'.now()->format('Y-m-d_His').'.zip';
        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("Could not create backup archive at {$path}.");
        }

        try {
            $this->addDatabase($zip);
            $this->addDirectory($zip, Storage::disk('local')->path('certificates'), 'certificates');

            $zip->addFromString('manifest.json', json_encode([
                'created_at' => now()->toIso8601String(),
                'app' => config('app.name'),
                'connection' => DB::connection()->getDriverName(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}');
        } finally {
            $zip->close();
        }

        $this->prune($directory);

        return $path;
    }

    private function addDatabase(ZipArchive $zip): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $database = (string) DB::connection()->getDatabaseName();

            if (! is_file($database)) {
                throw new RuntimeException("SQLite database not found at {$database}.");
            }

            $zip->addFile($database, 'database.sqlite');

            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $zip->addFromString('database.sql', $this->dumpMysql());

            return;
        }

        throw new RuntimeException("Database backups are not supported for the {$driver} driver.");
    }

    private function dumpMysql(): string
    {
        $config = DB::connection()->getConfig();

        $command = [
            (string) config('backup.mysqldump', 'mysqldump'),
            '--host='.($config['host'] ?? '127.0.0.1'),
            '--port='.($config['port'] ?? '3306'),
            '--user='.($config['username'] ?? 'root'),
            '--single-transaction',
            '--quick',
            '--routines',
            '--default-character-set='.($config['charset'] ?? 'utf8mb4'),
            (string) ($config['database'] ?? ''),
        ];

        $process = new Process($command, null, [
            'MYSQL_PWD' => (string) ($config['password'] ?? ''),
        ]);

        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('mysqldump failed: '.trim($process->getErrorOutput()));
        }

        return $process->getOutput();
    }

    private function addDirectory(ZipArchive $zip, string $path, string $prefix): void
    {
        if (! is_dir($path)) {
            return;
        }

        foreach (File::allFiles($path) as $file) {
            $relative = $prefix.'/'.str_replace('\\', '/', $file->getRelativePathname());

            $zip->addFile($file->getRealPath(), $relative);
        }
    }

    private function prune(string $directory): void
    {
        $days = (int) config('backup.keep_days', 14);

        if ($days <= 0) {
            return;
        }

        $cutoff = now()->subDays($days)->getTimestamp();

        foreach (File::glob($directory.'/backup-*.zip') as $file) {
            if (File::lastModified($file) < $cutoff) {
                File::delete($file);
            }
        }
    }
}
