<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{{ $c->certificate_no ?? 'Certificate Preview' }}</title>
<style>
    @font-face {
        font-family: 'NotoJP';
        font-weight: normal;
        src: url('{{ str_replace('\\', '/', $school['fonts']['regular'] ?? resource_path('fonts/NotoSansJP-Regular.ttf')) }}') format('truetype');
    }
    @font-face {
        font-family: 'NotoJP';
        font-weight: bold;
        src: url('{{ str_replace('\\', '/', $school['fonts']['bold'] ?? resource_path('fonts/NotoSansJP-Bold.ttf')) }}') format('truetype');
    }

    @page {
        size: 210mm 297mm;
        margin: 0;
    }

    * {
        margin: 0;
        padding: 0;
    }

    html, body {
        margin: 0;
        padding: 0;
        width: 210mm;
        font-family: 'NotoJP', sans-serif;
        color: #000000;
        font-size: 8.2pt;
        line-height: 11.2pt;
        background-color: #FFFFFF;
    }

    .bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 209.8mm;
        height: 296.5mm;
        z-index: -1;
    }

    .page {
        position: relative;
        z-index: 1;
        padding: 7mm 16mm 7mm;
    }

    .draft-overlay {
        position: fixed;
        top: 122mm;
        left: 0;
        width: 210mm;
        text-align: center;
        font-size: 52pt;
        font-weight: bold;
        letter-spacing: 10px;
        color: rgba(220, 38, 38, 0.16);
        z-index: 50;
    }

    /* ===== HEADER ===== */
    .header-block {
        text-align: center;
        margin-bottom: 3.2mm;
    }

    .header-logo {
        height: 16.5mm;
        margin: 0 auto 0.8mm;
        text-align: center;
        font-size: 0;
        line-height: 0;
    }

    .header-logo img {
        width: auto;
        height: auto;
        max-width: 36mm;
        max-height: 16.5mm;
        display: block;
        margin: 0 auto;
    }

    .institution-name {
        font-family: 'OldEnglish', 'Times-Bold', 'Times-Roman', 'NotoJP', serif;
        font-size: 16pt;
        font-weight: bold;
        color: #000000;
        letter-spacing: 0.4px;
        line-height: 18pt;
        margin-bottom: 0.5mm;
    }

    .cert-title-ja {
        font-size: 20pt;
        font-weight: bold;
        color: #000000;
        letter-spacing: 2px;
        line-height: 23pt;
        margin-bottom: 0.5mm;
    }

    .cert-title-en {
        font-size: 8.6pt;
        font-weight: normal;
        color: #000000;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        line-height: 10.5pt;
        margin-bottom: 1.5mm;
    }

    .gold-divider-wrap {
        width: 156mm;
        height: 1.8mm;
        margin: 0 auto;
        text-align: center;
        font-size: 0;
        line-height: 0;
    }

    .gold-divider-img {
        width: 156mm;
        height: 1.8mm;
        display: block;
        margin: 0 auto;
    }

    .gold-divider-fallback {
        width: 154mm;
        height: 0;
        border-top: 1.2pt solid #C49A3A;
        margin: 0.4mm auto 0;
    }

    /* ===== STATEMENT ===== */
    .statement-block {
        text-align: center;
        font-size: 8.4pt;
        line-height: 11.6pt;
        color: #000000;
        margin: 0 auto 3.2mm;
        width: 100%;
    }

    /* ===== METADATA KEY-VALUE TABLES (STUDENT & COURSE) ===== */
    .details-block {
        margin-bottom: 3.2mm;
    }

    .course-block {
        margin-bottom: 3.5mm;
    }

    table.meta-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.2pt;
        line-height: 11pt;
        color: #000000;
    }

    table.meta-table td {
        padding: 0.4mm 0;
        vertical-align: top;
        text-align: left;
    }

    table.meta-table.student-meta td.meta-label {
        width: 39mm;
        padding-right: 2mm;
        white-space: nowrap;
        color: #000000;
    }

    table.meta-table.course-meta td.meta-label {
        width: 67.5mm;
        padding-right: 2.5mm;
        white-space: nowrap;
        color: #000000;
    }

    table.meta-table td.meta-value {
        color: #000000;
    }

    .student-name {
        font-weight: bold;
        font-size: 9pt;
        color: #000000;
    }

    .meta-subnote {
        color: #000000;
    }

    /* ===== ATTENDANCE TABLE ===== */
    .attendance-section {
        margin-bottom: 3.2mm;
        page-break-inside: avoid;
    }

    .attendance-caption {
        text-align: center;
        font-size: 8.2pt;
        line-height: 10.5pt;
        color: #000000;
        margin-bottom: 0.9mm;
    }

    table.attendance-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.2pt;
    }

    table.attendance-table.compact {
        font-size: 7.5pt;
    }

    table.attendance-table.very-compact {
        font-size: 6.7pt;
    }

    table.attendance-table th,
    table.attendance-table td {
        border: 0.75pt solid #000000;
        padding: 1mm 0.7mm;
        text-align: center;
        vertical-align: middle;
        line-height: 9pt;
        color: #000000;
    }

    table.attendance-table.very-compact th,
    table.attendance-table.very-compact td {
        padding: 0.85mm 0.35mm;
        line-height: 8.2pt;
    }

    table.attendance-table th {
        font-weight: normal;
    }

    table.attendance-table td.row-label {
        text-align: left;
        padding-left: 1.5mm;
        white-space: nowrap;
        width: 31%;
    }

    table.attendance-table.compact td.row-label {
        width: 26%;
    }

    table.attendance-table.very-compact td.row-label {
        width: 20.5%;
        padding-left: 1mm;
    }

    table.attendance-table th.sum-col {
        width: 10.5%;
        white-space: nowrap;
    }

    .holidays-line {
        margin-top: 1.2mm;
        font-size: 8pt;
        line-height: 10.8pt;
        color: #000000;
    }

    .holidays-label {
        color: #000000;
    }

    /* ===== INSTRUCTORS & GOLD SEAL ===== */
    table.auth-row {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2mm;
        margin-bottom: 3mm;
        page-break-inside: avoid;
    }

    table.auth-row td {
        vertical-align: middle;
        text-align: center;
    }

    td.instructor-col {
        width: 37%;
        padding: 0 2mm;
    }

    td.seal-col {
        width: 26%;
        padding: 0 1mm;
    }

    .seal-wrap {
        height: 22mm;
        margin: 0 auto;
        text-align: center;
        font-size: 0;
        line-height: 0;
    }

    .seal-img {
        width: auto;
        height: auto;
        max-width: 28mm;
        max-height: 22mm;
        display: block;
        margin: 0 auto;
    }

    .instructor-name {
        font-size: 8.8pt;
        font-weight: bold;
        color: #000000;
        line-height: 11pt;
    }

    .instructor-title {
        font-size: 8pt;
        color: #000000;
        margin-top: 0.3mm;
        line-height: 10.5pt;
    }

    .instructor-mail {
        font-size: 7.4pt;
        color: #000000;
        margin-top: 0.3mm;
        line-height: 10pt;
    }

    .mail-link {
        color: #1D4ED8;
        text-decoration: underline;
    }

    /* ===== FOOTER & QR VERIFICATION ===== */
    table.footer-row {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5mm;
        page-break-inside: avoid;
    }

    td.footer-spacer {
        width: 26mm;
        vertical-align: bottom;
    }

    td.footer-center {
        text-align: center;
        vertical-align: bottom;
        font-size: 8pt;
        line-height: 10.8pt;
        color: #000000;
    }

    td.footer-qr {
        width: 22mm;
        padding-right: 4mm;
        text-align: center;
        vertical-align: bottom;
    }

    .qr-box {
        width: 14mm;
        height: 14mm;
        padding: 0.5mm;
        margin: 0 auto 0.4mm;
        border: 0.5pt solid #CBD5E1;
        background-color: #FFFFFF;
        font-size: 0;
        line-height: 0;
    }

    .qr-img {
        width: 14mm;
        height: 14mm;
        display: block;
    }

    .qr-placeholder {
        width: 14mm;
        height: 14mm;
        padding: 0.5mm;
        border: 0.7pt dashed #9CA3AF;
        margin: 0 auto 0.4mm;
    }

    .qr-cert-no {
        font-size: 5.2pt;
        font-weight: bold;
        color: #0F2656;
        white-space: nowrap;
        line-height: 6.5pt;
    }

    .qr-caption {
        font-size: 4.9pt;
        color: #111827;
        line-height: 6pt;
    }
</style>
</head>
<body>
@if (!empty($images['background']))
    <img class="bg" src="{{ $images['background'] }}" alt="">
@endif

@if ($draft)
    <div class="draft-overlay">DRAFT / 下書き</div>
@endif

@php
    $jaDays = $c->classDaysJapanese();
    $enDays = $c->class_days ?? [];
    $formatList = static function (array $items, string $conjunction): string {
        $count = count($items);
        if ($count === 0) {
            return '';
        }
        if ($count === 1) {
            return (string) $items[0];
        }
        $last = array_pop($items);
        return implode(', ', $items) . $conjunction . $last;
    };
    $classDaysJa = $formatList($jaDays, ' そして ');
    $classDaysEn = $formatList($enDays, ' and ');

    $monthCount = $c->attendances->count();
    $tableSizeClass = $monthCount > 9 ? 'very-compact' : ($monthCount > 6 ? 'compact' : '');

    $contextJa = implode('、', array_filter($c->course->learningContextJapanese()));
    $contextEn = implode(', ', array_filter($c->course->learningContextEnglish()));
    $stackLearningContext = $contextJa !== '' && $contextEn !== '' && (mb_strlen($contextJa) + mb_strlen($contextEn)) > 45;

    $instructors = $c->instructors->values();
    $leftInstructor = $instructors->get(0);
    $rightInstructor = $instructors->get(1);
@endphp

<div class="page">
    {{-- Header: Logo, Institution Name, Bilingual Title, Tapered Gold Divider --}}
    <div class="header-block">
        @if (!empty($images['logo']))
            <div class="header-logo">
                <img src="{{ $images['logo'] }}" alt="{{ $school['name'] }}">
            </div>
        @endif
        <div class="institution-name">{{ $school['name'] }}</div>
        <div class="cert-title-ja">日本語学習修了証明書</div>
        <div class="cert-title-en">JAPANESE LANGUAGE COURSE COMPLETION CERTIFICATE</div>
        <div class="gold-divider-wrap">
            @if (!empty($images['divider']))
                <img class="gold-divider-img" src="{{ $images['divider'] }}" alt="">
            @else
                <div class="gold-divider-fallback"></div>
            @endif
        </div>
    </div>

    {{-- Official Certification Statement --}}
    <div class="statement-block">
        下記の者は、本校において日本語学習課程を修了したことをここに証明します。<br>
        This is to certify that the following student has successfully completed the Japanese language course at our institution.
    </div>

    {{-- Student & Schedule Details --}}
    <div class="details-block">
        <table class="meta-table student-meta">
            <tr>
                <td class="meta-label">学生氏名 / Name:</td>
                <td class="meta-value">
                    <span class="student-name">{{ $c->student_name_en }}</span>@if ($c->student_name_ja) （{{ $c->student_name_ja }}）@endif
                </td>
            </tr>
            <tr>
                <td class="meta-label">生年月日 / Date of Birth:</td>
                <td class="meta-value">{{ $c->student_date_of_birth ? \App\Support\JapaneseDate::full($c->student_date_of_birth) : '—' }}</td>
            </tr>
            <tr>
                <td class="meta-label">性別 / Sex:</td>
                <td class="meta-value">{{ $c->studentGenderJapanese() }}</td>
            </tr>
            <tr>
                <td class="meta-label">学習期間 / Study Period:</td>
                <td class="meta-value">{{ ($c->study_start && $c->study_end) ? \App\Support\JapaneseDate::range($c->study_start, $c->study_end) : '—' }}</td>
            </tr>
            <tr>
                <td class="meta-label">学習時間 / Learning Time:</td>
                <td class="meta-value">
                    1日{{ $c->hours_per_day }}時間 週{{ $c->days_per_week }}日 / {{ $c->hours_per_day }} hours a day {{ $c->days_per_week }} days per week
                    @if ($classDaysJa !== '' || $classDaysEn !== '')
                        <br>{{ $classDaysJa }} / {{ $classDaysEn }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Monthly Attendance Matrix --}}
    <div class="attendance-section">
        <div class="attendance-caption">学習期間 / Study Period:</div>
        <table class="attendance-table {{ $tableSizeClass }}">
            <thead>
                <tr>
                    <th class="row-label"></th>
                    @foreach ($c->attendances as $a)
                        <th>{{ \App\Support\JapaneseDate::month($a->month) }}</th>
                    @endforeach
                    <th class="sum-col">合計 (sum)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="row-label">授業時間 / Class Hour</td>
                    @foreach ($c->attendances as $a)
                        <td>{{ $a->class_hours }}</td>
                    @endforeach
                    <td>{{ $c->total_class_hours }}</td>
                </tr>
                <tr>
                    <td class="row-label">出席時間 / Attendance Hour</td>
                    @foreach ($c->attendances as $a)
                        <td>{{ $a->attendance_hours }}</td>
                    @endforeach
                    <td>{{ $c->total_attendance_hours }}</td>
                </tr>
                <tr>
                    <td class="row-label">出席率 / Attendance Rate (%)</td>
                    @foreach ($c->attendances as $a)
                        <td>{{ $a->attendanceRate() }}%</td>
                    @endforeach
                    <td>{{ round((float) $c->attendance_rate) }}%</td>
                </tr>
            </tbody>
        </table>
        <div class="holidays-line">
            <span class="holidays-label">★他の休み / Other Holidays:</span> 祝日 / National Holidays: {{ $c->national_holidays ?: '—' }}
        </div>
    </div>

    {{-- Course Completion & Issue Details --}}
    <div class="course-block">
        <table class="meta-table course-meta">
            <tr>
                <td class="meta-label">履修内容 / Learning Context:</td>
                <td class="meta-value">
                    @if ($stackLearningContext)
                        {{ $contextJa }}<br>{{ $contextEn }}
                    @elseif ($contextJa !== '' && $contextEn !== '')
                        {{ $contextJa }} / {{ $contextEn }}
                    @else
                        {{ $contextJa ?: $contextEn }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="meta-label">コース名称 / Course Name:</td>
                <td class="meta-value">{{ $c->course->name_ja }} / {{ $c->course->name_en }}</td>
            </tr>
            <tr>
                <td class="meta-label">総履修時間 / Course Completion Time:</td>
                <td class="meta-value">
                    {{ $c->course->total_hours }} 時間 / {{ $c->course->total_hours }} Hours
                    @if ($c->study_start && $c->study_end)
                        <span class="meta-subnote">({{ \App\Support\JapaneseDate::range($c->study_start, $c->study_end) }})</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="meta-label">当該コース終了目標レベル / Learning Aim Level:</td>
                <td class="meta-value">{{ $c->course->aim_level_ja }} / {{ $c->course->aim_level_en }}</td>
            </tr>
            <tr>
                <td class="meta-label">使用教材 / Book Name:</td>
                <td class="meta-value">{{ $c->course->book_ja }} / {{ $c->course->book_en }}</td>
            </tr>
            @if ($c->issued_on)
                <tr>
                    <td class="meta-label">発行日 / Date of Issued:</td>
                    <td class="meta-value">{{ \App\Support\JapaneseDate::full($c->issued_on) }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- Dual Instructors & Center Gold Seal --}}
    <table class="auth-row">
        <tr>
            <td class="instructor-col">
                @if ($leftInstructor)
                    <div class="instructor-name">{{ $leftInstructor->name }}</div>
                    <div class="instructor-title">{{ $leftInstructor->title }}</div>
                    @if ($leftInstructor->email)
                        <div class="instructor-mail">メール / Mail: <span class="mail-link">{{ $leftInstructor->email }}</span></div>
                    @endif
                @endif
            </td>
            <td class="seal-col">
                @if (!empty($images['seal']))
                    <div class="seal-wrap">
                        <img class="seal-img" src="{{ $images['seal'] }}" alt="Official School Seal">
                    </div>
                @endif
            </td>
            <td class="instructor-col">
                @if ($rightInstructor)
                    <div class="instructor-name">{{ $rightInstructor->name }}</div>
                    <div class="instructor-title">{{ $rightInstructor->title }}</div>
                    @if ($rightInstructor->email)
                        <div class="instructor-mail">メール / Mail: <span class="mail-link">{{ $rightInstructor->email }}</span></div>
                    @endif
                @endif
            </td>
        </tr>
    </table>

    {{-- Institutional Footer & Compact QR Verification --}}
    <table class="footer-row">
        <tr>
            <td class="footer-spacer"></td>
            <td class="footer-center">
                学校名 / School Name: {{ $school['name'] }}<br>
                住所 / Address: {{ $school['address'] }}<br>
                電話 / TEL: {{ $school['phone'] }}
            </td>
            <td class="footer-qr">
                @if ($qrBase64)
                    <div class="qr-box">
                        <img class="qr-img" src="data:image/svg+xml;base64,{{ $qrBase64 }}" alt="QR Code">
                    </div>
                @else
                    <div class="qr-placeholder"></div>
                @endif
                @if ($c->certificate_no)
                    <div class="qr-cert-no">{{ $c->certificate_no }}</div>
                @endif
                <div class="qr-caption">Scan to verify</div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>