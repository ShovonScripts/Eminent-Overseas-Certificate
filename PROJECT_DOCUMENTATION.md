# Eminent Certificate System - Complete Project Documentation

## Overview
Laravel 12 application for issuing bilingual (Japanese/English) Japanese-course completion certificates as PDF with QR codes linking to public verification pages.

---

## Project Structure

### Core Application Files

#### Configuration
| File | Purpose |
|------|---------|
| `config/app.php` | Application configuration (timezone, locale, etc.) |
| `config/school.php` | School settings (name, address, phone, email, verify URL, certificate prefix, font/image paths) |
| `config/backup.php` | Backup configuration (path, retention days, mysqldump binary) |
| `config/cors.php` | CORS configuration |
| `config/database.php` | Database connections (SQLite for testing, MySQL for production) |
| `config/filesystems.php` | Filesystem disks (local, public) |
| `config/mail.php` | Mail configuration |
| `config/queue.php` | Queue configuration |
| `config/session.php` | Session driver (database) |

#### Environment
| File | Purpose |
|------|---------|
| `.env` | Environment variables (DB, mail, app URL, school info) |
| `.env.example` | Template for environment variables |

---

### Models (`app/Models/`)

#### Certificate.php
**Core model** - Represents a certificate with all student/course/attendance data inline.

**Key Fields:**
- `certificate_no` - Sequential number (EOC-2026-000001)
- `verification_code` - ULID (26 chars, unguessable)
- Student fields: `student_name_en`, `student_name_ja`, `student_date_of_birth`, `student_gender`, `student_email`, `student_phone`
- `course_id` - FK to courses
- `study_start`, `study_end` - Date range
- `hours_per_day`, `days_per_week`, `class_days` (JSON array)
- `national_holidays` - Text (semicolon-separated dates)
- `total_class_hours`, `total_attendance_hours`, `attendance_rate`
- `issued_on`, `issued_at`, `status` (draft/issued/revoked)
- `issued_by` - FK to users
- `revoked_at`, `revoke_reason`
- `pdf_path`, `pdf_sha256`

**Relationships:**
- `course()` - BelongsTo Course
- `attendances()` - HasMany CertificateAttendance
- `instructors()` - BelongsToMany Instructor (with pivot position)
- `issuedBy()` - BelongsTo User
- `verificationLogs()` - HasMany VerificationLog

**Accessors:**
- `verifyUrl()` - Full verification URL
- `isDraft()`, `isIssued()`, `isRevoked()`
- `classDaysJapanese()` - Japanese weekday names
- `studentGenderJapanese()` - Japanese gender label

#### Course.php
- `name_en`, `name_ja` - Course names
- `level_code` - N5, N4, etc.
- `aim_level_en`, `aim_level_ja` - Target levels
- `total_hours` - Total course hours
- `book_en`, `book_ja` - Textbook names
- `learning_context` - JSON array of {ja, en} items
- `default_hours_per_day`, `default_days_per_week`

#### Instructor.php
- `name`, `title` - Name and title (e.g., "講師 / Instructor")
- `email` - Contact email
- `signature_path` - Optional signature image
- `is_active` - Boolean

#### CertificateAttendance.php
- `certificate_id` - FK
- `year`, `month` - Year/month
- `class_hours`, `attendance_hours` - Hours
- `attendanceRate()` - Percentage

#### User.php
- `name`, `email`, `password`, `role` (super_admin/staff)
- 2FA fields: `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`
- Methods: `isSuperAdmin()`, `hasEnabledTwoFactor()`, `twoFactorRecoveryCodes()`

#### VerificationLog.php
- `certificate_id` - FK (nullable)
- `code_tried` - Verification code attempted
- `result` - valid/revoked/not_found
- `ip_address`, `user_agent`

#### Settings.php
- Key-value store for system configuration
- Methods: `get(key, default)`, `set(key, value)`

---

### Controllers (`app/Http/Controllers/`)

#### Admin Controllers (`app/Http/Controllers/Admin/`)

| Controller | Purpose |
|------------|---------|
| `DashboardController.php` | Admin dashboard with stats, recent certificates, verification logs |
| `CertificateController.php` | Full CRUD for certificates + suggest/issue/revoke/import |
| `CourseController.php` | CRUD for courses |
| `InstructorController.php` | CRUD for instructors |
| `UserController.php` | Staff account management (super admin only) |
| `VerificationLogController.php` | Audit log of verification attempts |
| `TwoFactorSecurityController.php` | 2FA enrollment/confirmation/disable |
| `SettingsController.php` | System settings (school info, verification URL, asset uploads) |

#### Auth Controllers (`app/Http/Controllers/Auth/`)

| Controller | Purpose |
|------------|---------|
| `LoginController.php` | Staff login/logout, redirects to 2FA challenge if enabled |
| `ForgotPasswordController.php` | Password reset link request |
| `ResetPasswordController.php` | Password reset with token |
| `TwoFactorChallengeController.php` | 2FA code/recovery code verification |

#### Public Controllers

| Controller | Purpose |
|------------|---------|
| `VerifyController.php` | Public verification pages (form, lookup, show) |
| `CertificateDownloadController.php` | Private PDF download (auth + policy) |
| `CertificatePreviewController.php` | Draft PDF preview with watermark (auth) |

---

### Services (`app/Services/`)

| Service | Purpose |
|---------|---------|
| `CertificateIssuer.php` | Orchestrates certificate issuing: validation, totals, number/code generation, PDF generation, status update |
| `CertificatePdfService.php` | Renders PDF using DomPDF with Noto Sans JP fonts, QR code, background/logo/seal images |
| `CertificateNumberGenerator.php` | Generates sequential per-year certificate numbers (EOC-YYYY-NNNNNN) |
| `VerificationCodeGenerator.php` | Generates ULID verification codes (26 chars) |
| `AttendanceSuggester.php` | Calculates monthly class hours from study period, class days, hours/day, holidays |
| `QrCodeService.php` | Generates QR code SVG as base64 |
| `CertificateCsvImporter.php` | Imports certificates from CSV with validation and bulk issuing |
| `BackupService.php` | Creates ZIP backup of database + certificate PDFs |
| `TwoFactorService.php` | TOTP secret generation, verification, provisioning URI, recovery codes |

---

### Policies (`app/Policies/`)

| Policy | Rules |
|--------|-------|
| `CertificatePolicy.php` | viewAny/create: all authenticated; update: draft only; delete: draft + super_admin; issue: draft; download: issued/revoked; revoke: issued + super_admin |
| `UserPolicy.php` | All actions: super_admin only; delete: cannot delete self or last super_admin |

---

### Middleware (`app/Http/Middleware/`)

| Middleware | Purpose |
|------------|---------|
| `LoadSettings.php` | Loads settings from DB into `config('school')` on every request |

---

### Console Commands (`app/Console/Commands/`)

| Command | Purpose |
|---------|---------|
| `CreateAdminCommand.php` | `php artisan admin:create` - Creates staff/super_admin accounts |
| `BackupCommand.php` | `php artisan backup:run` - Creates ZIP backup of DB + certificates |

---

### Requests (`app/Http/Requests/`)

| Request | Purpose |
|---------|---------|
| `CertificateRequest.php` | Validates certificate + inline student fields, attendance, instructors |
| `SuggestAttendanceRequest.php` | Validates suggest payload |
| `ImportCertificatesRequest.php` | Validates CSV upload |
| `UserRequest.php` | Validates staff account (name, email, role, password with strength) |
| `LoginRequest.php` | Login with rate limiting |
| `ImportCertificatesRequest.php` | CSV file validation |

---

### Views (`resources/views/`)

#### Layouts
| File | Purpose |
|------|---------|
| `layouts/admin.blade.php` | Admin layout with sidebar, topbar, flash messages |
| `layouts/guest.blade.php` | Public auth pages layout |

#### Admin Views
| Directory | Purpose |
|-----------|---------|
| `admin/dashboard.blade.php` | Dashboard with stats, recent certs, recent logs |
| `admin/certificates/index.blade.php` | Certificate list with filters, pagination |
| `admin/certificates/form.blade.php` | Create/edit certificate (student fields, course, schedule, attendance, instructors) |
| `admin/certificates/show.blade.php` | Certificate detail with actions (issue, revoke, download, preview) |
| `admin/certificates/import.blade.php` | CSV import with results |
| `admin/courses/index.blade.php` | Course list |
| `admin/courses/form.blade.php` | Create/edit course |
| `admin/courses/show.blade.php` | Course detail |
| `admin/instructors/index.blade.php` | Instructor list |
| `admin/instructors/form.blade.php` | Create/edit instructor |
| `admin/users/index.blade.php` | Staff accounts list (super admin) |
| `admin/users/form.blade.php` | Create/edit staff account |
| `admin/security.blade.php` | 2FA setup/enable/confirm/disable |
| `admin/logs/index.blade.php` | Verification logs table |
| `admin/settings/edit.blade.php` | System settings form with file uploads |

#### Auth Views
| File | Purpose |
|------|---------|
| `auth/login.blade.php` | Staff login form |
| `auth/forgot-password.blade.php` | Forgot password form |
| `auth/reset-password.blade.php` | Reset password form |
| `auth/two-factor-challenge.blade.php` | 2FA code/recovery code entry |

#### Public Verification Views
| File | Purpose |
|------|---------|
| `verify/form.blade.php` | Search by certificate number |
| `verify/valid.blade.php` | Valid certificate display |
| `verify/revoked.blade.php` | Revoked certificate notice |
| `verify/not-found.blade.php` | Not found page |

#### Certificate PDF Template
| File | Purpose |
|------|---------|
| `certificates/pdf.blade.php` | Full PDF template with bilingual fields, attendance table, signatures, QR code, draft watermark |

---

### Database Migrations (`database/migrations/`)

| Migration | Purpose |
|-----------|---------|
| `0001_01_01_000000_create_users_table.php` | Users + password_reset_tokens + sessions |
| `2026_01_01_000000_add_role_to_users_table.php` | Adds role column to users |
| `2026_01_01_000001_create_students_table.php` | ~~Students table (dropped)~~ |
| `2026_01_01_000002_create_courses_table.php` | Courses |
| `2026_01_01_000003_create_instructors_table.php` | Instructors |
| `2026_01_01_000004_create_certificates_table.php` | Certificates with student fields inline |
| `2026_01_01_000005_create_certificate_attendances_table.php` | Monthly attendance |
| `2026_01_01_000006_create_certificate_instructor_table.php` | Pivot with position |
| `2026_01_01_000007_create_verification_logs_table.php` | Audit logs |
| `2026_01_01_000008_add_two_factor_to_users_table.php` | 2FA columns |
| `2026_01_01_000009_add_student_fields_to_certificates_table.php` | Student fields on certificates |
| `2026_01_01_000010_drop_students_table.php` | Drops students table |
| `2026_11_07_142121_create_settings_table.php` | System settings key-value store |

---

### Seeders (`database/seeders/`)

| Seeder | Purpose |
|--------|---------|
| `DatabaseSeeder.php` | Calls CourseSeeder, InstructorSeeder |
| `CourseSeeder.php` | Creates Basic Level (N5) and Intermediate (N4) courses |
| `InstructorSeeder.php` | Creates default instructors |

---

### Factories (`database/factories/`)

| Factory | Purpose |
|---------|---------|
| `UserFactory.php` | Creates users with hashed password |
| `CourseFactory.php` | Creates courses |
| `InstructorFactory.php` | Creates instructors |
| `CertificateFactory.php` | Creates certificates with states (issued, revoked, withAttendance, withInstructors) |

---

### Tests (`tests/`)

#### Feature Tests
| Test File | Coverage |
|-----------|----------|
| `AdminCertificateTest.php` | Certificate CRUD, validation, suggest, issue/revoke |
| `AdminPanelTest.php` | Dashboard, resource access, permissions |
| `AdminUserManagementTest.php` | Staff CRUD, super_admin restrictions |
| `AuthTest.php` | Login, logout, invalid credentials, admin:create |
| `BackupCommandTest.php` | SQLite backup creation |
| `CertificateDownloadTest.php` | PDF download/preview, permissions |
| `CertificateImportTest.php` | CSV import with valid/invalid rows |
| `CertificateImmutabilityTest.php` | Issued cert cannot be edited/deleted |
| `CertificateIssueTest.php` | Issuing flow, validation, PDF generation |
| `CertificateNumberGeneratorTest.php` | Sequential per-year numbering |
| `PasswordResetTest.php` | Forgot/reset password flow |
| `TwoFactorAuthenticationTest.php` | 2FA enrollment, login challenge, recovery codes, disable |
| `VerifyCertificateTest.php` | Public verify (valid/revoked/not_found), rate limiting |

---

### Frontend Assets

| File | Purpose |
|------|---------|
| `resources/css/app.css` | Complete custom CSS (admin layout, forms, tables, auth, responsive) |
| `resources/js/app.js` | Minimal JS (Vite entry) |
| `vite.config.js` | Vite configuration |
| `package.json` | Dependencies (Tailwind CSS 4, Vite, Laravel Vite plugin) |

---

### Documentation (`docs/`)

| File | Topic |
|------|-------|
| `01-requirements.md` | Field requirements from source certificate |
| `02-architecture.md` | Stack, flow, folder structure, design decisions |
| `03-database.md` | Tables, columns, ERD |
| `04-installation.md` | Local setup steps |
| `05-certificate-generation.md` | PDF template, fonts, services |
| `06-qr-verification.md` | QR codes, public verify pages |
| `07-routes-and-api.md` | Web routes, optional JSON API |
| `08-admin-guide.md` | Staff workflow for issuing/revoking |
| `09-security.md` | Anti-forgery, hardening checklist |
| `10-deployment.md` | Production server, queues, backups |
| `11-testing.md` | Test plan, sample tests |
| `12-roadmap.md` | Milestones, future features |

---

## Key Workflows

### 1. Issue a Certificate
```
1. Admin → Certificates → New certificate
2. Fill student details (name EN/JA, DOB, gender, email, phone)
3. Select course (auto-fills defaults)
4. Set study dates, hours/day, class days, holidays
5. Click "Suggest class hours" → auto-calculates monthly class hours
6. Enter attendance hours per month (≤ class hours)
7. Select 1-2 instructors (left/right positions)
8. Set issue date (≥ study end)
9. Save draft → Click "Issue certificate" → PDF + QR generated
10. Download PDF, print A4 100% scale
```

### 2. Public Verification
```
1. Scan QR on certificate OR visit /verify
2. Enter certificate number or scan QR → redirects to /verify/{code}
3. Shows: Certificate No., Student name, Course, Study period, Total hours, Attendance rate, Issue date
4. No DOB, phone, email exposed
5. Every attempt logged in verification_logs
```

### 3. Revoke Certificate
```
1. Super admin opens issued certificate
2. Clicks "Revoke certificate"
3. Enters reason → certificate status = revoked
6. QR page now shows "Revoked" banner with date/reason
```

### 4. Bulk Import
```
1. Admin → Certificates → Import CSV
2. Download template → fill rows with student_name_en, student_date_of_birth, etc.
3. Upload CSV → each row validated → valid rows issued immediately
3. Results show issued count + errors per row
```

### 5. Backup
```
php artisan backup:run
# Creates storage/app/backups/backup-YYYY-MM-DD_HHMMSS.zip
# Contains: database.sqlite (or mysqldump) + certificates/ PDFs + manifest.json
# Scheduled daily at 02:00 via routes/console.php
```

---

## Configuration Reference

### `.env` Critical Variables
```env
APP_NAME="Eminent Certificates"
APP_URL=http://localhost:8000          # Must match verify_base_url domain
APP_TIMEZONE=Asia/Dhaka
DB_CONNECTION=mysql                     # or sqlite for testing
DB_DATABASE=eminent_certificates
DB_USERNAME=root
DB_PASSWORD=
CERTIFICATE_VERIFY_BASE_URL="${APP_URL}/verify"
SCHOOL_NAME="Eminent Overseas & Consultants"
SCHOOL_ADDRESS="16/9, Indira Road, Behind Tejgaon College, Dhaka 1215"
SCHOOL_PHONE="+88 01816877795"
SCHOOL_EMAIL="info@eminentoverseas.uk"
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
```

### School Config (`config/school.php`)
All values loaded from DB via `LoadSettings` middleware, falling back to env.

---

## Production Deployment Checklist

1. **Database**: MySQL 8+, run migrations
2. **Environment**: Set all `.env` vars, `APP_ENV=production`, `APP_DEBUG=false`
3. **Optimize**: `composer install --optimize-autoloader --no-dev && php artisan config:cache && php artisan route:cache && php artisan view:cache`
4. **Queue Worker**: `php artisan queue:work --daemon` (supervisor)
5. **Scheduler**: Cron `* * * * * php artisan schedule:run`
5. **Storage Link**: `php artisan storage:link` (for public asset access)
6. **HTTPS**: Configure SSL, set `APP_URL=https://yourdomain.com`, `CERTIFICATE_VERIFY_BASE_URL=https://yourdomain.com/verify`
7. **Backups**: Verify `backup:run` works, test restore

---

## Security Notes

- **No sequential IDs in public URLs** - Only ULID verification codes
- **Certificate immutability** - Observer prevents updates after issuing
- **2FA** - TOTP with recovery codes, enforced per-user
- **Rate limiting** - Verify endpoint (30/min), login (5/attempt), password reset (6/min)
- **Private PDF storage** - Served via authenticated controller with policy check
- **No PII on verify page** - Only certificate data, no DOB/phone/email
- **Audit trail** - Every verification attempt logged with IP, UA, result

---

## Development Commands

```bash
# Install dependencies
composer install && npm install && npm run build

# Database
php artisan migrate --seed
php artisan admin:create  # Create super admin

# Testing
php artisan test --compact
./vendor/bin/pint         # Code style

# Development
php artisan serve
npm run dev               # Vite dev server

# Production build
npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## File Upload Locations

| Asset | Storage Path | Public URL |
|-------|-------------|------------|
| Logo | `storage/app/public/images/certificate/` | `/storage/images/certificate/{file}` |
| Background | `storage/app/public/images/certificate/` | `/storage/images/certificate/{file}` |
| Seal | `storage/app/public/images/certificate/` | `/storage/images/certificate/{file}` |
| Certificate PDFs | `storage/app/certificates/` | Served via controller (private) |

Run `php artisan storage:link` to create `public/storage` symlink.

---

## Extending the System

### Add New Course Level
1. Admin → Courses → New
2. Fill name (EN/JA), level_code (N3, N2, N1), total_hours, book, context
3. Certificates can now select it

### Add Instructor
1. Admin → Instructors → New
2. Name, title, email, optional signature upload
4. Appears in certificate form dropdowns

### Customize Certificate PDF
Edit `resources/views/certificates/pdf.blade.php` - modify HTML/CSS, fields, layout.

### Add Settings
1. Add row to `settings` table or use SettingsController
2. Access via `config('school.key')` or `Settings::get('key')`

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| PDF not generating | Check Noto Sans JP fonts in `resources/fonts/`, verify DomPDF can write to `storage/app/temp` |
| QR not scanning | Verify `verify_base_url` is correct public HTTPS URL, check QR size in PDF |
| Session lost on dev server | Use `SESSION_DRIVER=file` locally, or ensure same domain for all requests |
| 2FA not working | Check system time sync, ensure `APP_KEY` is stable |
| CSV import fails | Check column names match template exactly, dates in YYYY-MM-DD |

---

## License
Internal project of Eminent Overseas & Consultants. All rights reserved.