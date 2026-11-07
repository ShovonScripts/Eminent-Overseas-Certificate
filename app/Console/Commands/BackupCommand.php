<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Throwable;

class BackupCommand extends Command
{
    protected $signature = 'backup:run';

    protected $description = 'Create a zip archive of the database and issued certificate PDFs';

    public function handle(BackupService $backups): int
    {
        try {
            $path = $backups->create();
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Backup written to '.$path);
        $this->line('Size: '.number_format(filesize($path) / 1024, 1).' KB');

        return self::SUCCESS;
    }
}
