# Eminent Certificate System

A Laravel application for generating, issuing, and verifying student **Japanese Language Course Completion Certificates** (日本語学習修了証明書) for **Eminent Overseas & Consultants**, Dhaka.

Every issued certificate is a bilingual (Japanese / English) A4 PDF carrying a **QR code**. Scanning the QR opens a public page that confirms the certificate is genuine, or shows that it has been revoked.

## Features

| Area | What it does |
|---|---|
| Student records | Store student profile (name, date of birth, sex, contact) |
| Course templates | Reusable course definitions (Basic / N5, book, learning context, aim level) |
| Certificate builder | Study period, schedule, monthly class/attendance hours, holidays, issue date |
| Auto calculation | Totals, attendance rate, per-month table, Japanese date formatting (2025年6月2日) |
| PDF generation | A4 PDF matching the current Word design, Japanese font embedded |
| QR verification | Unique, non-guessable code per certificate, public verify page |
| Revocation | Mark a certificate revoked; QR then shows "Revoked" |
| Audit log | Every verification attempt is logged (IP, time, result) |
| Bulk issue | Planned: import a CSV and generate many certificates at once |

## Documentation index

| # | File | Contents |
|---|---|---|
| 1 | [docs/01-requirements.md](docs/01-requirements.md) | Fields taken from the current certificate, business rules |
| 2 | [docs/02-architecture.md](docs/02-architecture.md) | Stack, packages, folder structure, flow diagrams |
| 3 | [docs/03-database.md](docs/03-database.md) | Tables, columns, migrations, ERD |
| 4 | [docs/04-installation.md](docs/04-installation.md) | Local setup step by step |
| 5 | [docs/05-certificate-generation.md](docs/05-certificate-generation.md) | PDF template, Japanese fonts, service code |
| 6 | [docs/06-qr-verification.md](docs/06-qr-verification.md) | QR code and public verification page |
| 7 | [docs/07-routes-and-api.md](docs/07-routes-and-api.md) | Web routes, optional JSON API |
| 8 | [docs/08-admin-guide.md](docs/08-admin-guide.md) | How staff issue and revoke certificates |
| 9 | [docs/09-security.md](docs/09-security.md) | Anti-forgery and hardening checklist |
| 10 | [docs/10-deployment.md](docs/10-deployment.md) | Production server, queues, backups |
| 11 | [docs/11-testing.md](docs/11-testing.md) | Test plan and sample tests |
| 12 | [docs/12-roadmap.md](docs/12-roadmap.md) | Build order (milestones) and future ideas |
| - | [CLAUDE.md](CLAUDE.md) | Instructions for AI coding assistants working on this repo |

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan admin:create
npm install && npm run build
php artisan serve
```

Full steps are in [docs/04-installation.md](docs/04-installation.md).

## Tech stack

- PHP 8.3+, Laravel 12 or newer
- MySQL 8 / MariaDB 10.6+ (SQLite for tests)
- Custom Laravel Blade admin panel
- dompdf for PDF (Browsershot is a drop-in alternative, see doc 5)
- simple-qrcode for QR generation
- Noto Sans JP font for Japanese text

## Verification URL format

```
https://certificates.eminentoverseas.uk/verify/01JH8Z3K6Q9X4M2V7B5N1D0T8R
```

(The domain is an example; use your own.)

## License

Internal project of Eminent Overseas & Consultants. All rights reserved.
