# 6. QR code and verification

## 6.1 How it works

1. On issue, the system creates a random **verification code** (ULID, 26 characters).
2. The QR code encodes: `https://certificates.example.com/verify/{code}`.
3. Anyone scanning it opens a public page that looks the certificate up in the database and shows its status.
4. The PDF itself is not needed for verification. The database is the source of truth.

## 6.2 Why not put the student name or the certificate number in the QR?
The number is sequential and guessable. A random code prevents people from enumerating other students' certificates.

## 6.3 Code generators

```php
// app/Services/VerificationCodeGenerator.php
use Illuminate\Support\Str;
class VerificationCodeGenerator {
    public function make(): string {
        do { $code = (string) Str::ulid(); }
        while (Certificate::where('verification_code', $code)->exists());
        return $code;
    }
}
```

```php
// app/Services/CertificateNumberGenerator.php  -> EOC-2026-000001
class CertificateNumberGenerator {
    public function make(CarbonInterface $issuedOn): string {
        return DB::transaction(function () use ($issuedOn) {
            $year = $issuedOn->year;
            $last = Certificate::withTrashed()->where('certificate_no','like',"EOC-$year-%")
                     ->lockForUpdate()->max('certificate_no');
            $next = $last ? ((int) substr($last, -6)) + 1 : 1;
            return sprintf('%s-%d-%06d', config('school.number_prefix'), $year, $next);
        });
    }
}
```

## 6.4 QR service

```php
// app/Services/QrCodeService.php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function svgBase64(string $url): string
    {
        return base64_encode(
            QrCode::format('svg')->size(300)->margin(1)->errorCorrection('M')->generate($url)
        );
    }
}
```

`Certificate` model helper:

```php
public function verifyUrl(): string
{
    return rtrim(config('school.verify_base_url'), '/') . '/' . $this->verification_code;
}
```

Error correction level **M** (15%) is enough for a clean print. Use **Q/H** only if you add a logo in the middle of the QR.

Print size: keep the QR at least **22 mm x 22 mm** with a quiet zone of 2 mm.

## 6.5 Public verify controller

```php
// app/Http/Controllers/VerifyController.php
class VerifyController extends Controller
{
    public function show(Request $request, string $code)
    {
        $cert = Certificate::with(['student:id,name_en','course'])
                ->where('verification_code', $code)->first();

        $result = match (true) {
            ! $cert                                  => 'not_found',
            $cert->status === CertificateStatus::Revoked => 'revoked',
            $cert->status === CertificateStatus::Issued  => 'valid',
            default                                  => 'not_found',   // drafts are invisible
        };

        VerificationLog::create([
            'certificate_id' => $cert?->id,
            'code_tried'     => substr($code, 0, 64),
            'result'         => $result,
            'ip_address'     => $request->ip(),
            'user_agent'     => substr((string) $request->userAgent(), 0, 255),
        ]);

        return response()->view("verify.$result", compact('cert'), $result === 'not_found' ? 404 : 200)
               ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
```

## 6.6 What the verify page shows

**Valid**

```
✔ Certificate is VALID
Certificate No.   EOC-2026-000001
Student           Md. Anik
Course            日本語初級 / Basic Level  (JLPT N5)
Study period      2025-06-02 to 2025-10-27
Total hours       180 hours  (attendance 100%)
Issued on         2026-01-15
Issued by         Eminent Overseas & Consultants
```

**Revoked**: red banner, "This certificate has been revoked", revoke date, no personal details beyond the certificate number.

**Not found**: "No certificate matches this code."

Never show: date of birth, phone, email, address, photo.

## 6.7 Manual verification (no phone)
Add a form at `/verify` where a person types the **certificate number** printed under the QR (for example `EOC-2026-000001`). The server finds the certificate and redirects to `/verify/{code}`.

Because certificate numbers are guessable, this form must be strictly rate limited (for example 10 requests per minute per IP) and the result page still shows only the limited public data from 6.6.

## 6.8 Optional: tamper check
Add an upload box: "Upload the PDF you received". The server computes SHA-256 and compares it with `pdf_sha256`. A match proves that the file is the original.

## 6.9 Rate limiting

```php
// bootstrap/app.php or RouteServiceProvider
RateLimiter::for('verify', fn (Request $r) => Limit::perMinute(30)->by($r->ip()));
```
```php
Route::get('/verify/{code}', [VerifyController::class,'show'])
     ->middleware('throttle:verify')->where('code','[0-9A-HJKMNP-TV-Z]{26}');
```

## 6.10 Testing the QR
- Scan the printed PDF with 2 or 3 phones (iOS camera, Android camera, a QR app).
- Print at 100% scale on a normal office printer.
- Scan from a PDF displayed on a screen, and from a photocopy.
