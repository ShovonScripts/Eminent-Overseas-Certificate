# 10. Deployment

## 10.1 Server options

| Option | Notes |
|---|---|
| VPS (DigitalOcean, Hetzner, AWS Lightsail) + Nginx | Recommended, full control |
| Laravel Forge / Ploi | Easiest managed VPS deploys |
| Shared cPanel hosting | Works with dompdf. Point the domain at `public/`. Avoid Browsershot |

## 10.2 Ubuntu + Nginx outline

```bash
sudo apt install nginx mysql-server php8.3-fpm php8.3-{mbstring,xml,intl,gd,zip,mysql,bcmath,curl} supervisor
cd /var/www && git clone <repo> certificates && cd certificates
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate
php artisan migrate --force
php artisan optimize
php artisan config:cache && php artisan route:cache && php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
```

Nginx server block (essentials):

```nginx
server {
    server_name certificates.example.com;
    root /var/www/certificates/public;
    index index.php;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ { include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php8.3-fpm.sock; }
    location ~ /\.(?!well-known) { deny all; }
}
```

Then `sudo certbot --nginx -d certificates.example.com` for HTTPS.

## 10.3 Queue worker (Supervisor)

```ini
[program:certificates-worker]
command=php /var/www/certificates/artisan queue:work --sleep=3 --tries=3 --max-time=3600
user=www-data
autostart=true
autorestart=true
numprocs=1
```

## 10.4 Production `.env` essentials
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://certificates.example.com
CERTIFICATE_VERIFY_BASE_URL=https://certificates.example.com/verify
SESSION_SECURE_COOKIE=true
```

> **Important:** The domain in `CERTIFICATE_VERIFY_BASE_URL` is printed inside every QR code. If you change the domain later, old QR codes stop working. Choose a permanent domain now and keep old domains redirecting forever.

## 10.5 Backups
- Nightly `mysqldump` + `storage/app/certificates` to off-site storage (S3, Backblaze, or another server).
- Keep 30 days of daily backups, 12 months of monthly backups.
- Test a restore every quarter. The database is the proof of validity of every certificate ever issued.

## 10.6 Monitoring
- Uptime monitor on `/verify` (UptimeRobot, Better Stack).
- Laravel log alerts for exceptions (Sentry or Flare).
- Disk space alert (PDF storage grows).

## 10.7 Updating
```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan queue:restart
```
