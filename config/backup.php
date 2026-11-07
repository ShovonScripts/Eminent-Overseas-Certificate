<?php

declare(strict_types=1);

return [
    'path' => storage_path('app/backups'),

    'keep_days' => (int) env('BACKUP_KEEP_DAYS', 14),

    'mysqldump' => env('DB_DUMP_BINARY', 'mysqldump'),
];
