# 4. Installation (local)

## 4.1 Requirements
- PHP 8.3+ with extensions: `mbstring`, `intl`, `gd`, `zip`, `xml`, `pdo_mysql`, `bcmath`
- Composer 2, Node 20+, MySQL 8 or MariaDB 10.6+

## 4.2 Create the project
```bash
composer create-project laravel/laravel eminent-certificates
cd eminent-certificates
cp .env.example .env
php artisan key:generate
```

## 4.3 Install packages
```bash
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
npm install
```

## 4.4 Environment (`.env`)
```ini
APP_NAME="Eminent Certificates"
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Dhaka

DB_CONNECTION=mysql
DB_DATABASE=eminent_certificates
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

# Used inside the QR code. Must be the PUBLIC domain in production.
CERTIFICATE_VERIFY_BASE_URL="${APP_URL}/verify"

SCHOOL_NAME="Eminent Overseas & Consultants"
SCHOOL_ADDRESS="16/9, Indira Road, Behind Tejgaon College, Dhaka 1215"
SCHOOL_PHONE="+88 01816877795"
SCHOOL_EMAIL="info@eminentoverseas.uk"
```

## 4.5 Config file `config/school.php`
```php
<?php
return [
    'name'    => env('SCHOOL_NAME'),
    'address' => env('SCHOOL_ADDRESS'),
    'phone'   => env('SCHOOL_PHONE'),
    'email'   => env('SCHOOL_EMAIL'),
    'verify_base_url' => env('CERTIFICATE_VERIFY_BASE_URL'),
    'number_prefix'   => 'EOC',
];
```

## 4.6 Assets
1. Export from your Word file: logo, border/background, seal. In a `.docx`, rename to `.zip` and open `word/media/` (the background is `image1.png`, 1377x1948 px).
2. Put them in `public/images/certificate/` (`background.png`, `logo.png`, `seal.png`).
3. Download Noto Sans JP (Regular and Bold) from Google Fonts and place the TTFs in `resources/fonts/`.

## 4.7 Database
```bash
php artisan migrate
php artisan db:seed
php artisan admin:create           # create the first staff / super-admin login
npm run build
php artisan storage:link            # for signature images only
php artisan queue:table && php artisan migrate
```

## 4.8 Run
```bash
php artisan serve
php artisan queue:work              # second terminal
```
Admin panel: `http://localhost:8000/admin`

## 4.9 Test the QR on your phone
`localhost` is not reachable from your phone. Use your LAN IP (`php artisan serve --host=0.0.0.0`, set `APP_URL=http://192.168.x.x:8000`) or a tunnel such as Cloudflare Tunnel or ngrok.
