# 7. Routes and API

## 7.1 Web routes (`routes/web.php`)

```php
// Public
Route::get('/', fn () => redirect('/verify'));
Route::get('/verify', [VerifyController::class, 'form'])->name('verify.form');
Route::post('/verify', [VerifyController::class, 'lookup'])->middleware('throttle:10,1')->name('verify.lookup');
Route::get('/verify/{code}', [VerifyController::class, 'show'])
     ->middleware('throttle:verify')->where('code', '[0-9A-HJKMNP-TV-Z]{26}')->name('verify.show');

// Authenticated staff
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::middleware('auth')->group(function () {
    Route::get('/certificates/{certificate}/pdf', CertificateDownloadController::class)
         ->name('certificates.pdf');
    Route::get('/certificates/{certificate}/preview', CertificatePreviewController::class)
         ->name('certificates.preview');
});

// Custom admin panel
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('students', StudentController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('instructors', InstructorController::class)->except(['show']);
    Route::resource('certificates', CertificateController::class);
    Route::post('certificates/suggest', [CertificateController::class, 'suggest'])->name('certificates.suggest');
    Route::post('certificates/{certificate}/issue', [CertificateController::class, 'issue'])->name('certificates.issue');
    Route::post('certificates/{certificate}/revoke', [CertificateController::class, 'revoke'])->name('certificates.revoke');
    Route::get('verification-logs', [VerificationLogController::class, 'index'])->name('logs.index');
});
```

The admin panel is a custom Blade UI at `/admin`, protected by Laravel session auth.

## 7.2 Route table

| Method | URL | Auth | Purpose |
|---|---|---|---|
| GET | /verify | no | Search form |
| POST | /verify | no | Lookup by certificate number, redirects |
| GET | /verify/{code} | no | QR landing page |
| GET | /login | guest | Staff sign-in form |
| POST | /login | guest | Staff sign-in |
| GET | /certificates/{id}/pdf | staff | Download PDF (private storage) |
| GET | /admin | staff | Dashboard with stats and recent activity |
| GET/POST | /admin/students | staff | Student records |
| GET/POST | /admin/courses | staff | Course templates |
| GET/POST | /admin/instructors | staff | Instructor names and signatures |
| GET/POST | /admin/certificates | staff | Certificate list and draft editor |
| POST | /admin/certificates/{id}/issue | staff | Issue and generate the PDF |
| POST | /admin/certificates/{id}/revoke | super admin | Revoke an issued certificate |
| GET | /admin/verification-logs | staff | Audit trail of QR/page verifications |

## 7.3 Optional JSON API

Useful if another system (website, mobile app, partner school or employer) needs to check certificates.

`GET /api/v1/verify/{code}`

```json
{
  "valid": true,
  "status": "issued",
  "certificate_no": "EOC-2026-000001",
  "student_name": "Md. Anik",
  "course": { "name_ja": "日本語初級", "name_en": "Basic Level", "level": "N5" },
  "study_start": "2025-06-02",
  "study_end": "2025-10-27",
  "total_hours": 180,
  "attendance_rate": 100,
  "issued_on": "2026-01-15"
}
```

Revoked:

```json
{ "valid": false, "status": "revoked", "certificate_no": "EOC-2026-000001", "revoked_at": "2026-02-01" }
```

Not found: HTTP 404 `{ "valid": false, "status": "not_found" }`.

Implementation: `app/Http/Resources/PublicCertificateResource.php` exposes only the fields above. Apply the same `verify` throttle. No API key is needed for the public endpoint.

## 7.4 Policies

| Ability | Super admin | Staff |
|---|---|---|
| create / update draft | yes | yes |
| issue | yes | yes |
| download | yes | yes |
| revoke | yes | no (configurable) |
| delete | yes | no |
