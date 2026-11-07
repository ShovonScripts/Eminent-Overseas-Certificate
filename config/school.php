<?php

declare(strict_types=1);

return [
    'name' => env('SCHOOL_NAME', 'Eminent Overseas & Consultants'),
    'address' => env('SCHOOL_ADDRESS', '16/9, Indira Road, Behind Tejgaon College, Dhaka 1215'),
    'phone' => env('SCHOOL_PHONE', '+88 01816877795'),
    'email' => env('SCHOOL_EMAIL', 'info@eminentoverseas.uk'),
    'verify_base_url' => env('CERTIFICATE_VERIFY_BASE_URL', rtrim((string) env('APP_URL', 'http://localhost'), '/').'/verify'),
    'number_prefix' => env('CERTIFICATE_NUMBER_PREFIX', 'EOC'),
    'fonts' => [
        'regular' => resource_path('fonts/NotoSansJP-Regular.ttf'),
        'bold' => resource_path('fonts/NotoSansJP-Bold.ttf'),
    ],
    'images' => [
        'background' => public_path('images/certificate/background.png'),
        'logo' => public_path('images/certificate/logo.png'),
        'seal' => public_path('images/certificate/seal.png'),
    ],
];
