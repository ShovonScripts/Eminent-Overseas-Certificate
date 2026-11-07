# 5. Certificate generation (PDF)

## 5.1 Layout (from the current Word file)

A4 portrait, full-page background image (blue and gold border, watermark pattern).

1. Logo, school name "Eminent Overseas & Consultants"
2. Title: **日本語学習修了証明書** / JAPANESE LANGUAGE COURSE COMPLETION CERTIFICATE
3. Statement (JA + EN)
4. Student block: name, date of birth, sex, study period, learning time, class days
5. Monthly attendance table
6. Holidays, learning context, course name, completion time, aim level, book, issue date
7. Footer: two instructor blocks with a gold seal between them, then school name, address, phone
8. **New:** QR code with certificate number and short URL (bottom left or right corner)

## 5.2 Japanese date helper

```php
// app/Support/JapaneseDate.php
namespace App\Support;

use Carbon\CarbonInterface;

class JapaneseDate
{
    public static function full(CarbonInterface $d): string
    {
        return "{$d->year}年{$d->month}月{$d->day}日";   // 2025年6月2日
    }

    public static function range(CarbonInterface $a, CarbonInterface $b): string
    {
        return self::full($a).' ～ '.self::full($b);
    }

    public static function gender(string $g): string
    {
        return match ($g) { 'male' => '男性', 'female' => '女性', default => 'その他' };
    }

    public static function weekday(string $en): string
    {
        return ['Monday'=>'月曜日','Tuesday'=>'火曜日','Wednesday'=>'水曜日',
                'Thursday'=>'木曜日','Friday'=>'金曜日','Saturday'=>'土曜日','Sunday'=>'日曜日'][$en];
    }
}
```

## 5.3 Calculations

```php
// inside CertificateIssuer or a model method
$totalClass  = $certificate->attendances->sum('class_hours');
$totalAttend = $certificate->attendances->sum('attendance_hours');
$rate        = $totalClass > 0 ? round($totalAttend / $totalClass * 100, 2) : 0;
```

Auto-suggest monthly class hours:

```php
public function suggestHours(Carbon $start, Carbon $end, array $days, int $hoursPerDay, array $holidays): array
{
    $byMonth = [];
    foreach (CarbonPeriod::create($start, $end) as $date) {
        if (in_array($date->englishDayOfWeek, $days) && ! in_array($date->toDateString(), $holidays)) {
            $key = $date->format('Y-m');
            $byMonth[$key] = ($byMonth[$key] ?? 0) + $hoursPerDay;
        }
    }
    return $byMonth;   // staff can still adjust each value
}
```

## 5.4 Blade template skeleton

`resources/views/certificates/pdf.blade.php`

```blade
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<style>
  @font-face { font-family:'NotoJP'; font-weight:normal;
               src:url('{{ storage_path('fonts/NotoSansJP-Regular.ttf') }}') format('truetype'); }
  @font-face { font-family:'NotoJP'; font-weight:bold;
               src:url('{{ storage_path('fonts/NotoSansJP-Bold.ttf') }}') format('truetype'); }
  @page { size: A4 portrait; margin: 0; }
  body { font-family:'NotoJP', sans-serif; font-size:10.5pt; color:#030503; margin:0; }
  .bg  { position:fixed; top:0; left:0; width:210mm; height:297mm; z-index:-1; }
  .page{ padding:38mm 24mm 18mm 24mm; }
  h1   { text-align:center; font-size:20pt; margin:0; letter-spacing:2px; }
  .sub { text-align:center; font-size:11pt; margin-bottom:6mm; }
  table.att { width:100%; border-collapse:collapse; font-size:9.5pt; }
  table.att td, table.att th { border:1px solid #000; padding:2px 4px; text-align:center; }
  .sign { width:40%; text-align:center; }
  .qr   { position:absolute; bottom:14mm; left:22mm; width:24mm; font-size:6.5pt; text-align:center; }
</style>
</head>
<body>
<img class="bg" src="{{ public_path('images/certificate/background.png') }}">
<div class="page">

  <div style="text-align:center">
    <img src="{{ public_path('images/certificate/logo.png') }}" height="60">
  </div>

  <h1>日本語学習修了証明書</h1>
  <div class="sub">JAPANESE LANGUAGE COURSE COMPLETION CERTIFICATE</div>

  <p style="text-align:center">
    下記の者は、本校において日本語学習課程を修了したことをここに証明します。<br>
    This is to certify that the following student has successfully completed
    the Japanese language course at our institution.
  </p>

  <p>学生氏名/Name: <b>{{ $c->student->name_en }}</b></p>
  <p>生年月日/Date of Birth: {{ JapaneseDate::full($c->student->date_of_birth) }}</p>
  <p>性別/Sex: {{ JapaneseDate::gender($c->student->gender) }}</p>
  <p>学習期間/Study Period: {{ JapaneseDate::range($c->study_start, $c->study_end) }}</p>
  <p>学習時間/Learning Time: 1日{{ $c->hours_per_day }}時間 週{{ $c->days_per_week }}日 /
     {{ $c->hours_per_day }} hours a day {{ $c->days_per_week }} days per week</p>

  {{-- monthly table --}}
  <table class="att">
    <tr><th></th>
      @foreach($c->attendances as $a)<th>{{ $a->month }}月</th>@endforeach
      <th>合計(sum)</th></tr>
    <tr><td>授業時間/Class Hour</td>
      @foreach($c->attendances as $a)<td>{{ $a->class_hours }}</td>@endforeach
      <td>{{ $c->total_class_hours }}</td></tr>
    <tr><td>出席時間/Attendance Hour</td>
      @foreach($c->attendances as $a)<td>{{ $a->attendance_hours }}</td>@endforeach
      <td>{{ $c->total_attendance_hours }}</td></tr>
    <tr><td>出席率/Attendance Rate (%)</td>
      @foreach($c->attendances as $a)
        <td>{{ round($a->attendance_hours / max($a->class_hours,1) * 100) }}%</td>
      @endforeach
      <td>{{ round($c->attendance_rate) }}%</td></tr>
  </table>

  {{-- remaining course lines, instructors, footer: same pattern --}}

  <div class="qr">
    <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" width="80">
    <div>{{ $c->certificate_no }}</div>
    <div>Scan to verify</div>
  </div>
</div>
</body>
</html>
```

> dompdf needs `isRemoteEnabled` only for remote images. We use local paths and a base64 QR so it stays `false`.

## 5.5 PDF service

```php
// app/Services/CertificatePdfService.php
namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificatePdfService
{
    public function __construct(private QrCodeService $qr) {}

    public function generate(Certificate $c): string
    {
        $c->load(['student','course','instructors','attendances']);

        $pdf = Pdf::loadView('certificates.pdf', [
            'c'        => $c,
            'qrBase64' => $this->qr->svgBase64($c->verifyUrl()),
        ])->setPaper('a4', 'portrait')
          ->setOption(['dpi' => 150, 'defaultFont' => 'NotoJP', 'isFontSubsettingEnabled' => true]);

        $binary = $pdf->output();
        $path   = "certificates/{$c->certificate_no}.pdf";
        Storage::disk('local')->put($path, $binary);

        $c->update(['pdf_path' => $path, 'pdf_sha256' => hash('sha256', $binary)]);
        return $path;
    }
}
```

Copy the TTFs to `storage/fonts/` (dompdf's font cache directory) or register them once with `php artisan` / the dompdf font loader. If Japanese shows as `????` or empty boxes, the font is not being picked up, check the path and file name.

## 5.6 Alternative: Browsershot (best fidelity)

```bash
composer require spatie/laravel-pdf
npm install puppeteer
```
```php
use Spatie\LaravelPdf\Facades\Pdf;
Pdf::view('certificates.pdf', ['c' => $c, 'qrBase64' => $qr])
   ->format('a4')->margins(0,0,0,0)->save(storage_path("app/certificates/{$c->certificate_no}.pdf"));
```
Use this if you want the PDF to look **pixel-identical** to the browser preview or use advanced CSS (flex, grid). It needs Node and Chromium on the server.

## 5.7 Preview before issuing
Add a "Preview" action in the admin certificate page that streams the PDF with a **DRAFT** watermark and without a QR code, so a draft can never be mistaken for a real certificate.

## 5.8 Common issues

| Problem | Fix |
|---|---|
| Japanese shows as boxes | Font not registered. Check TTF path, clear `storage/fonts` cache |
| Background image missing | Use `public_path()` not `asset()` |
| Table breaks to second page | Reduce padding / font size; keep everything on one A4 |
| QR blurry in print | Use SVG QR, not low-res PNG |
| Different look vs Word | Tune the CSS; or switch to Browsershot |
