<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\BackupService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class BackupCommandTest extends TestCase
{
    public function test_a_sqlite_backup_is_created_and_contains_the_database(): void
    {
        Storage::fake('local');

        $database = tempnam(sys_get_temp_dir(), 'eminent-sqlite-');
        file_put_contents($database, 'sqlite-test-content');

        $backupDirectory = storage_path('app/backups-test');

        File::deleteDirectory($backupDirectory);
        config([
            'database.connections.sqlite.database' => $database,
            'backup.path' => $backupDirectory,
        ]);
        DB::purge('sqlite');

        try {
            $path = app(BackupService::class)->create();

            $this->assertFileExists($path);

            $zip = new ZipArchive;
            $this->assertTrue($zip->open($path));

            $this->assertSame('sqlite-test-content', $zip->getFromName('database.sqlite'));
            $this->assertNotFalse($zip->getFromName('manifest.json'));

            $zip->close();
        } finally {
            File::deleteDirectory($backupDirectory);
            File::delete($database);
        }
    }

    public function test_a_backup_includes_certificate_pdfs_from_the_local_disk(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('certificates/EOC-TEST-0001.pdf', '%PDF-test-bytes');

        $database = tempnam(sys_get_temp_dir(), 'eminent-sqlite-');
        file_put_contents($database, 'sqlite-test-content');

        $backupDirectory = storage_path('app/backups-test');

        File::deleteDirectory($backupDirectory);
        config([
            'database.connections.sqlite.database' => $database,
            'backup.path' => $backupDirectory,
        ]);
        DB::purge('sqlite');

        try {
            $path = app(BackupService::class)->create();

            $zip = new ZipArchive;
            $this->assertTrue($zip->open($path));

            $this->assertSame('%PDF-test-bytes', $zip->getFromName('certificates/EOC-TEST-0001.pdf'));

            $zip->close();
        } finally {
            File::deleteDirectory($backupDirectory);
            File::delete($database);
        }
    }

    public function test_the_backup_command_reports_the_archive(): void
    {
        Storage::fake('local');

        $database = tempnam(sys_get_temp_dir(), 'eminent-sqlite-');
        file_put_contents($database, 'sqlite-test-content');
        $backupDirectory = storage_path('app/backups-test');

        config([
            'database.connections.sqlite.database' => $database,
            'backup.path' => $backupDirectory,
        ]);
        DB::purge('sqlite');

        try {
            $this->artisan('backup:run')->assertSuccessful();
        } finally {
            File::deleteDirectory($backupDirectory);
            File::delete($database);
        }
    }
}
