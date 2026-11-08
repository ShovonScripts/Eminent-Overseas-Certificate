<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>{{ $c->certificate_no ?? 'Certificate Preview' }}</title>
<style>
    @font-face {
        font-family: 'NotoJP';
        font-weight: normal;
        src: url('{{ storage_path('fonts/notojp_normal_47bedc5b8344a9f3589c91ab34658f4f.ttf') }}') format('truetype');
    }
    @font-face {
        font-family: 'NotoJP';
        font-weight: bold;
        src: url('{{ storage_path('fonts/notojp_bold_479bec163307bb377a5591f9a8e403bb.ttf') }}') format('truetype');
    }

    @page {
        size: 210mm 297mm;
        margin: 0;
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    html, body {
        margin: 0;
        padding: 0;
        font-family: 'NotoJP', sans-serif;
        color: #0F172A;
        font-size: 8pt;
        line-height: 1.25;
        background-color: #FFFFFF;
        -webkit-font-smoothing: antialiased;
    }
    .bg {
        position: absolute;
        top: -3mm;
        left: -3mm;
        width: 210mm;
        height: 297mm;
        z-index: 0;
        display: block;
    }
    .page-content {
        position: relative;
        z-index: 1;
    }
    .page {
        position: relative;
        margin: 3mm;
        min-height: 250mm;
        padding: 10mm 16mm 6mm;
        border: 1.2pt solid #B08A45;
        outline: 0.5pt solid #D9C69E;
        outline-offset: -3mm;
    }

    /* Top Metadata Bar */
    .top-bar {
        width: 100%;
        margin-bottom: 2mm;
    }
    .top-bar td {
        vertical-align: middle;
        font-size: 8pt;
        color: #334155;
    }
    .top-bar-left {
        text-align: left;
        letter-spacing: 0.3px;
    }
    .top-bar-left strong {
        color: #0F172A;
    }
    .top-bar-right {
        text-align: right;
        font-weight: bold;
        color: #1E3A8A;
        font-size: 8.2pt;
        letter-spacing: 0.5px;
    }

    /* Header Section */
    .header-layout {
        width: 100%;
        margin-bottom: 2mm;
    }
    .header-logo-col {
        width: 20%;
        text-align: left;
        vertical-align: middle;
    }
    .header-logo-col img {
        max-height: 16mm;
        max-width: 37mm;
    }
    .header-center-col {
        width: 60%;
        text-align: center;
        vertical-align: middle;
    }
    .header-date-col {
        width: 20%;
        text-align: right;
        vertical-align: middle;
    }
    .institution-name {
        font-size: 11pt;
        font-weight: bold;
        letter-spacing: 1.4px;
        color: #0B192C;
        text-transform: uppercase;
        margin-bottom: 0.5mm;
    }
    .cert-title-japanese {
        font-size: 22pt;
        font-weight: bold;
        letter-spacing: 6px;
        color: #0B192C;
        margin: 0.8mm 0 0.5mm;
        line-height: 1.15;
    }
    .cert-title-english {
        font-size: 9pt;
        font-weight: bold;
        letter-spacing: 1.4px;
        color: #A16207;
        text-transform: uppercase;
        margin-bottom: 0.3mm;
    }
    .issue-date-box {
        font-size: 7pt;
        color: #334155;
        line-height: 1.35;
        text-align: right;
    }
    .issue-date-box strong {
        font-size: 7.5pt;
        color: #0F172A;
    }

    /* Statement Section */
    .statement-container {
        text-align: center;
        background: #F8FAFC;
        border-top: 0.75pt solid #CBD5E1;
        border-bottom: 0.75pt solid #CBD5E1;
        padding: 1.5mm 4mm;
        margin: 0.8mm auto 1.8mm;
        width: 100%;
        border: 0.7pt solid #D9C69E;
    }
    .statement-text-ja {
        font-size: 8.8pt;
        font-weight: 500;
        color: #0F172A;
        line-height: 1.35;
    }
    .statement-text-en {
        font-size: 7.8pt;
        color: #334155;
        margin-top: 0.4mm;
        line-height: 1.25;
    }

    /* Section Headers */
    .section-header {
        font-size: 8.4pt;
        font-weight: bold;
        color: #1E3A8A;
        letter-spacing: 0.5px;
        border-bottom: 0.9pt solid #1E3A8A;
        padding-bottom: 0.6mm;
        margin: 1.5mm 0 0.8mm;
        text-transform: uppercase;
    }

    /* Structured Particulars 4-Column Grid */
    table.particulars-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5mm;
    }
    table.particulars-table td {
        padding: 1mm 1.4mm;
        vertical-align: middle;
        font-size: 7.7pt;
        line-height: 1.15;
        border-bottom: 0.5pt solid #E2E8F0;
    }
    table.particulars-table td.field-label {
        width: 25%;
        color: #1E3A8A;
        font-weight: bold;
        background: #F8FAFC;
        white-space: nowrap;
        font-size: 7.3pt;
        border-right: 0.5pt solid #E2E8F0;
    }
    table.particulars-table td.field-val {
        width: 25%;
        color: #0F172A;
        border-right: 0.5pt solid #E2E8F0;
    }
    table.particulars-table td.field-val-wide {
        width: 75%;
        color: #0F172A;
    }
    .name-primary {
        font-size: 11pt;
        font-weight: bold;
        color: #0B192C;
    }
    .name-secondary {
        font-size: 7.2pt;
        color: #475569;
        font-weight: normal;
        margin-left: 1.8mm;
    }
    .contact-row td { background: #FBFAF7; }

    /* Attendance Records Matrix */
    table.attendance-matrix {
        width: 100%;
        border-collapse: collapse;
        margin: 1mm 0 1.4mm;
        font-size: 7.6pt;
        page-break-inside: avoid;
    }
    table.attendance-matrix th, 
    table.attendance-matrix td {
        border: 0.5pt solid #CBD5E1;
        padding: 1mm 0.6mm;
        text-align: center;
        vertical-align: middle;
        line-height: 1.2;
    }
    table.attendance-matrix th {
        background: #1E3A8A;
        color: #FFFFFF;
        font-weight: bold;
        font-size: 7.4pt;
    }
    table.attendance-matrix td.matrix-row-title {
        width: 28%;
        text-align: left;
        padding-left: 2mm;
        font-weight: bold;
        color: #1E3A8A;
        background: #F8FAFC;
        white-space: nowrap;
    }
    table.attendance-matrix th.sum-column {
        background: #0B192C;
        color: #FBBF24;
        width: 15%;
    }
    table.attendance-matrix td.sum-cell {
        background: #FEF3C7;
        color: #78350F;
        font-weight: bold;
    }
    table.attendance-matrix td.sum-rate {
        background: #FEF3C7;
        color: #065F46;
        font-weight: bold;
        font-size: 8.4pt;
    }

    /* Authentication, Signatures & Validation Section */
    table.auth-layout {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.8mm;
        page-break-inside: avoid;
    }
    table.auth-layout td {
        vertical-align: bottom;
    }
    .auth-qr-block {
        width: 25%;
        text-align: left;
        padding-right: 2mm;
    }
    .auth-qr-block img {
        width: 18mm;
        height: 18mm;
        display: block;
    }
    .auth-qr-text {
        font-size: 6.4pt;
        color: #334155;
        margin-top: 0.8mm;
        line-height: 1.15;
    }
    .auth-qr-code {
        font-weight: bold;
        font-size: 7pt;
        color: #1E3A8A;
    }
    .auth-instructors-block {
        width: 53%;
        text-align: center;
    }
    .auth-seal-block {
        width: 22%;
        text-align: center;
        vertical-align: middle;
    }
    .auth-seal-block img {
        width: 19mm;
        height: 19mm;
    }
    .auth-seal-caption {
        font-size: 6.6pt;
        color: #475569;
        margin-top: 0.8mm;
    }

    /* Instructor Signatures Grid */
    table.signatures-table {
        width: 100%;
        border-collapse: collapse;
    }
    table.signatures-table td {
        width: 50%;
        text-align: center;
        padding: 0 2.5mm;
        vertical-align: bottom;
    }
    .signature-ruler {
        border-bottom: 0.8pt solid #1E3A8A;
        margin-bottom: 1.5mm;
        height: 5.5mm;
    }
    .instructor-full-name {
        font-size: 8pt;
        font-weight: bold;
        color: #0B192C;
    }
    .instructor-prof-title {
        font-size: 6.8pt;
        color: #1E3A8A;
        margin-top: 0.3mm;
    }
    .instructor-email-addr {
        font-size: 6.2pt;
        color: #475569;
        margin-top: 0.2mm;
    }

    /* Institutional Footer */
    .institutional-footer {
        border-top: 0.6pt solid #CBD5E1;
        margin-top: 1.8mm;
        padding-top: 0.8mm;
        text-align: center;
        font-size: 6.8pt;
        color: #334155;
        line-height: 1.35;
    }
    .institutional-footer strong {
        color: #0F172A;
    }

    /* Draft Watermark */
    .draft-overlay {
        position: absolute;
        top: 110mm;
        left: 0;
        width: 210mm;
        text-align: center;
        font-size: 56pt;
        font-weight: 800;
        letter-spacing: 12px;
        color: rgba(220, 38, 38, 0.16);
        pointer-events: none;
        z-index: 50;
    }
</style>
</head>
<body>
<div class="page">
    @if ($images['background'])
        <img class="bg" src="{{ $images['background'] }}" alt="">
    @endif
    <div class="page-content">
    @if ($draft)
        <div class="draft-overlay">DRAFT / 下書き</div>
    @endif

    {{-- Top Metadata Row --}}
    <table class="top-bar">
        <tr>
            <td class="top-bar-left">
                認定校 / Accredited Japanese Institution: <strong>{{ $school['name'] }}</strong>
            </td>
            <td class="top-bar-right">
                @if ($c->certificate_no)
                    証明書番号 / Certificate No. {{ $c->certificate_no }}
                @else
                    下書き / Draft Certificate
                @endif
            </td>
        </tr>
    </table>

    {{-- Header Banner --}}
    <table class="header-layout">
        <tr>
            <td class="header-logo-col">
                @if ($images['logo'])
                    <img src="{{ $images['logo'] }}" alt="{{ $school['name'] }}">
                @endif
            </td>
            <td class="header-center-col">
                <div class="institution-name">{{ $school['name'] }}</div>
                <div class="cert-title-japanese">修 了 証 明 書</div>
                <div class="cert-title-english">Certificate of Japanese Language Course Completion</div>
            </td>
            <td class="header-date-col">
                @if ($c->issued_on)
                    <div class="issue-date-box">
                        発行年月日 / Date of Issue:<br>
                        <strong>{{ \App\Support\JapaneseDate::full($c->issued_on) }}</strong>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Official Certification Statement Box --}}
    <div class="statement-container">
        <div class="statement-text-ja">
            上記の者は、本校において規定の日本語学習課程を修了したことをここに証明します。
        </div>
        <div class="statement-text-en">
            This is to certify that the student named below has successfully completed the prescribed Japanese language course at our institution.
        </div>
    </div>

    {{-- Academic & Student Particulars: 4-Column Balanced Grid --}}
    <div class="section-header">学習者及び修了課程事項 / Academic & Student Particulars</div>
    <table class="particulars-table">
        <tr>
            <td class="field-label">学生氏名 / Name</td>
            <td class="field-val-wide" colspan="3">
                <span class="name-primary">{{ $c->student_name_en }}</span>
                @if ($c->student_name_ja)
                    <span class="name-secondary">（{{ $c->student_name_ja }}）</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="field-label">生年月日 / Date of Birth</td>
            <td class="field-val">{{ \App\Support\JapaneseDate::full($c->student_date_of_birth) }}</td>
            <td class="field-label">性別 / Sex</td>
            <td class="field-val">{{ $c->studentGenderJapanese() }}{{ $c->student_gender ? ' / '.$c->student_gender->label() : '' }}</td>
        </tr>
        @if ($c->student_email || $c->student_phone)
        <tr class="contact-row">
            <td class="field-label">連絡先 / Contact</td>
            <td class="field-val-wide" colspan="3">
                @if ($c->student_email) <strong>Email</strong> &nbsp;{{ $c->student_email }} @endif
                @if ($c->student_email && $c->student_phone) &nbsp;&nbsp;|&nbsp;&nbsp; @endif
                @if ($c->student_phone) <strong>Phone</strong> &nbsp;{{ $c->student_phone }} @endif
            </td>
        </tr>
        @endif
        <tr>
            <td class="field-label">コース名称 / Course Name</td>
            <td class="field-val"><strong>{{ $c->course->name_ja }}</strong> / {{ $c->course->name_en }}</td>
            <td class="field-label">終了目標 / Aim Level</td>
            <td class="field-val"><strong>{{ $c->course->aim_level_ja }}</strong> / {{ $c->course->aim_level_en }}</td>
        </tr>
        <tr>
            <td class="field-label">学習期間 / Study Period</td>
            <td class="field-val">{{ \App\Support\JapaneseDate::range($c->study_start, $c->study_end) }}</td>
            <td class="field-label">総履修時間 / Total Hours</td>
            <td class="field-val"><strong>{{ $c->course->total_hours }}時間</strong> / {{ $c->course->total_hours }} Hours</td>
        </tr>
        <tr>
            <td class="field-label">学習日程 / Schedule</td>
            <td class="field-val">1日{{ $c->hours_per_day }}時間 週{{ $c->days_per_week }}日 ({{ $c->hours_per_day }}h/day, {{ $c->days_per_week }}d/wk)</td>
            <td class="field-label">授業日 / Class Days</td>
            <td class="field-val">{{ implode('、', $c->classDaysJapanese()) }} ({{ implode(', ', $c->class_days ?? []) }})</td>
        </tr>
        <tr>
            <td class="field-label">使用教材 / Book Name</td>
            <td class="field-val">{{ $c->course->book_ja }} / {{ $c->course->book_en }}</td>
            <td class="field-label">他の休み / Holidays</td>
            <td class="field-val">{{ $c->national_holidays ?: '—' }}</td>
        </tr>
        <tr>
            <td class="field-label">履修内容 / Learning Context</td>
            <td class="field-val-wide" colspan="3">
                <strong>{{ implode('、', $c->course->learningContextJapanese()) }}</strong><br>
                <span style="color: #475569; font-size: 7.5pt;">{{ implode(', ', $c->course->learningContextEnglish()) }}</span>
            </td>
        </tr>
    </table>

    {{-- Monthly Attendance Matrix --}}
    <div class="section-header">出席状況詳細 / Monthly Attendance Records</div>
    <table class="attendance-matrix">
        <thead>
            <tr>
                <th style="width: 25%;">月度 / Month</th>
                @foreach ($c->attendances as $a)
                    <th>{{ \App\Support\JapaneseDate::month($a->month) }}</th>
                @endforeach
                <th class="sum-column">合計 / Sum</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="matrix-row-title">授業時間 / Class Hours</td>
                @foreach ($c->attendances as $a)
                    <td>{{ $a->class_hours }}</td>
                @endforeach
                <td class="sum-cell">{{ $c->total_class_hours }}h</td>
            </tr>
            <tr>
                <td class="matrix-row-title">出席時間 / Attendance Hours</td>
                @foreach ($c->attendances as $a)
                    <td>{{ $a->attendance_hours }}</td>
                @endforeach
                <td class="sum-cell">{{ $c->total_attendance_hours }}h</td>
            </tr>
            <tr>
                <td class="matrix-row-title">出席率 / Attendance Rate</td>
                @foreach ($c->attendances as $a)
                    <td>{{ $a->attendanceRate() }}%</td>
                @endforeach
                <td class="sum-rate">{{ round((float) $c->attendance_rate) }}%</td>
            </tr>
        </tbody>
    </table>

    {{-- Authentication, Dual Signatures, QR & Seal --}}
    <table class="auth-layout">
        <tr>
            {{-- Digital Cryptographic Verification Block --}}
            <td class="auth-qr-block">
                @if ($qrBase64)
                    <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" alt="QR Code">
                    <div class="auth-qr-text">
                        <div class="auth-qr-code">{{ $c->certificate_no }}</div>
                        <div>スキャン照会 / Scan to Verify</div>
                        <div style="font-size: 6.2pt; color: #64748B;">{{ $c->verification_code }}</div>
                    </div>
                @else
                    <div style="font-size: 7pt; color: #94A3B8; border: 0.5pt dashed #CBD5E1; padding: 5mm 2mm; text-align: center;">
                        QR Verification<br>(Issued Only)
                    </div>
                @endif
            </td>

            {{-- Authorizing Instructors Signatures --}}
            <td class="auth-instructors-block">
                <table class="signatures-table">
                    <tr>
                        @php $instructors = $c->instructors->take(2); @endphp
                        @foreach ($instructors as $instructor)
                            <td>
                                <div class="signature-ruler"></div>
                                <div class="instructor-full-name">{{ $instructor->name }}</div>
                                <div class="instructor-prof-title">{{ $instructor->title }}</div>
                                @if ($instructor->email)
                                    <div class="instructor-email-addr">{{ $instructor->email }}</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>

            {{-- Official Institution Seal (Inkan Stamp) --}}
            <td class="auth-seal-block">
                @if ($images['seal'])
                    <img src="{{ $images['seal'] }}" alt="Official School Seal">
                    <div class="auth-seal-caption">学校印 / Institutional Seal</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Institutional Accreditation Footer --}}
    <div class="institutional-footer">
        <strong>{{ $school['name'] }}</strong> &middot; {{ $school['address'] }}<br>
        Tel: {{ $school['phone'] }} &nbsp;|&nbsp; Email: {{ $school['email'] }} &nbsp;|&nbsp; Accredited Japanese Language Course Certification
    </div>
</div>
</div>
</body>
</html>
