# CLAUDE.md

Guidance for AI coding assistants (Claude Code etc.) working in this repository.

## Project
Laravel app that issues bilingual (JA/EN) Japanese-course completion certificates as PDF with a QR code that links to a public verification page. Read `README.md` and `docs/` first.

## Commands
```bash
composer install && npm install && npm run build
php artisan migrate --seed
php artisan admin:create
php artisan test
php artisan serve
./vendor/bin/pint            # code style
```

## Conventions
- PHP 8.3, strict types, Laravel conventions, PSR-12 via Pint.
- Business logic lives in `app/Services` (never in controllers or Blade).
- Certificate numbers and verification codes are generated only by `CertificateNumberGenerator` and `VerificationCodeGenerator`.
- Dates are stored as DATE columns; Japanese formatting (`2025年6月2日`) is done by `App\Support\JapaneseDate`.
- Public pages must never expose date of birth, phone, or email.
- Use Form Requests for validation, Policies for authorization.

## Rules you must not break
1. Never use sequential IDs in public URLs. Use `verification_code` (ULID).
2. An issued certificate's data is immutable. To correct it, revoke and re-issue.
3. Every call to the verify endpoint writes a `verification_logs` row.
4. The PDF must embed Noto Sans JP; do not rely on system fonts.
5. Do not commit `.env`, signature images, or generated PDFs.

## Testing
Feature tests for issue, verify (valid / revoked / unknown), and PDF generation are required for any change in those areas.

## Layout reference
Design follows `Eminent_Overseas___Consultants_Certificate.docx` (A4 portrait, blue and gold border, logo top centre, two instructor signatures and a seal at the bottom).
