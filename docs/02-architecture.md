# 2. Architecture

## 2.1 Stack

| Layer | Choice | Reason |
|---|---|---|
| Framework | Laravel 12+ (PHP 8.3+) | Your requirement, mature ecosystem |
| Admin UI | Laravel Blade + controllers | Purpose-built issuing workflow, no admin framework |
| Database | MySQL 8 / MariaDB | Standard hosting support |
| PDF | `barryvdh/laravel-dompdf` | Pure PHP, no server binaries |
| PDF (alternative) | `spatie/laravel-pdf` + Browsershot (Chromium) | Perfect CSS and font shaping, needs Node + Chromium |
| QR | `simplesoftwareio/simple-qrcode` | SVG/PNG output, no Imagick needed for SVG |
| Queue | database or Redis | Bulk PDF generation |
| Font | Noto Sans JP (TTF) | Japanese glyph coverage |

## 2.2 High-level flow

```
Staff (admin UI)          Laravel                         Public
      |                      |                               |
      | create certificate   |                               |
      |--------------------->| validate + calculate totals   |
      | click "Issue"        |                               |
      |--------------------->| generate number + code        |
      |                      | build QR (verify URL)         |
      |                      | render Blade -> PDF           |
      |                      | save PDF + SHA-256            |
      |<---------------------| download PDF                  |
      |                                                      |
      |      printed / emailed certificate with QR           |
      |----------------------------------------------------->|
      |                      |   scan QR -> GET /verify/{code}
      |                      |<------------------------------|
      |                      | lookup + log attempt          |
      |                      |------ VALID / REVOKED ------->|
```

## 2.3 Folder structure

```
app/
├── Enums/
│   ├── CertificateStatus.php        # draft | issued | revoked
│   └── Gender.php
├── Http/Controllers/
│   ├── Auth/LoginController.php     # staff sign in / out
│   ├── Admin/                       # dashboard + CRUD + issue/revoke
│   ├── VerifyController.php         # public verify page
│   └── CertificateDownloadController.php
├── Models/
│   ├── Student.php  Course.php  Instructor.php
│   ├── Certificate.php  CertificateAttendance.php  VerificationLog.php
├── Services/
│   ├── CertificateIssuer.php        # orchestrates issuing
│   ├── CertificateNumberGenerator.php
│   ├── VerificationCodeGenerator.php
│   ├── CertificatePdfService.php
│   └── QrCodeService.php
├── Support/
│   └── JapaneseDate.php             # 2025年6月2日 formatter
├── Policies/CertificatePolicy.php
└── Jobs/GenerateCertificatePdf.php
config/school.php                    # school name, address, phone, email, logo
resources/views/
├── admin/                         # dashboard, CRUD pages, certificate editor
├── auth/login.blade.php
├── certificates/pdf.blade.php       # the certificate design
└── verify/{valid,revoked,not-found}.blade.php
resources/fonts/NotoSansJP-Regular.ttf, NotoSansJP-Bold.ttf
public/images/ (not public pdfs)  logo.png  border.png  seal.png
storage/app/certificates/            # private PDFs
database/migrations/
database/seeders/CourseSeeder.php  InstructorSeeder.php
tests/Feature/
```

## 2.4 Design decisions

1. **Services, not fat controllers.** All issuing logic sits in `CertificateIssuer` so it can be called from the admin controllers, CLI, queue, and tests.
2. **Private PDF storage.** PDFs are saved on the `local` disk and served through an authorized controller. The public only sees the verify page.
3. **Verification code is separate from certificate number.** The number (`EOC-2026-000001`) is guessable; the code (ULID) is not. Only the code goes in the QR URL.
4. **Snapshot data.** At issue time, the monthly table and key values are stored on the certificate so later edits to a course or student never change an issued certificate.
5. **Immutability.** After issuing, update is blocked in the model (`updating` observer) except for status/revocation fields.

## 2.5 Certificate lifecycle

```
draft ──issue──> issued ──revoke──> revoked
  ^                 |
  └── (edit allowed)└── (edit blocked; re-issue as a new certificate)
```
