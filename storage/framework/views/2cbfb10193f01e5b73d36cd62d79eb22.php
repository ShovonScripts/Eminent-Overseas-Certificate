<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title><?php echo e($c->certificate_no ?? 'Certificate Preview'); ?></title>
    <style>
        @font-face {
            font-family: 'NotoJP';
            font-weight: normal;
            src: url('<?php echo e(str_replace('\\', '/', $school['fonts']['regular'] ?? resource_path('fonts/NotoSansJP-Regular.ttf'))); ?>') format('truetype');
        }

        @font-face {
            font-family: 'NotoJP';
            font-weight: bold;
            src: url('<?php echo e(str_replace('\\', '/', $school['fonts']['bold'] ?? resource_path('fonts/NotoSansJP-Bold.ttf'))); ?>') format('truetype');
        }

        @page {
            size: 210mm 297mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 210mm;
            font-family: 'NotoJP', sans-serif;
            color: #111111;
            font-size: 8pt;
            line-height: 10.2pt;
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
            padding: 9mm 16mm 6mm;
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
            margin-bottom: 1.6mm;
        }

        .header-logo {
            height: 15mm;
            margin: 0 auto 0.6mm;
            text-align: center;
            font-size: 0;
            line-height: 0;
        }

        .header-logo img {
            width: auto;
            height: auto;
            max-width: 40mm;
            max-height: 15mm;
            display: block;
            margin: 0 auto;
        }

        .institution-name {
            font-family: 'OldEnglish', 'Times-Bold', 'Times-Roman', 'NotoJP', serif;
            font-size: 15.5pt;
            font-weight: bold;
            color: #111111;
            letter-spacing: 0.4px;
            line-height: 17pt;
            margin-bottom: 0.6mm;
        }

        .cert-title-ja {
            font-size: 19pt;
            font-weight: bold;
            color: #111111;
            letter-spacing: 2px;
            line-height: 21.5pt;
            margin-bottom: 0.6mm;
        }

        .cert-title-en {
            font-size: 8.2pt;
            font-weight: normal;
            color: #4A4235;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 9.8pt;
            margin-bottom: 1.8mm;
        }

        .gold-divider-wrap {
            width: 156mm;
            height: 1.4mm;
            margin: 0 auto;
            text-align: center;
            font-size: 0;
            line-height: 0;
        }

        .gold-divider-img {
            width: 156mm;
            height: 1.4mm;
            display: block;
            margin: 0 auto;
        }

        .gold-divider-fallback {
            width: 154mm;
            height: 0;
            border-top: 1pt solid #C49A3A;
            margin: 0.3mm auto 0;
        }

        /* ===== STATEMENT ===== */
        .statement-block {
            text-align: center;
            font-size: 8.2pt;
            line-height: 11.2pt;
            color: #222222;
            margin: 0 auto 1.6mm;
            width: 165mm;
        }

        .statement-block .ja {
            margin-bottom: 0.9mm;
        }

        .statement-block .en {
            font-size: 8pt;
            line-height: 10.8pt;
            color: #333333;
        }

        /* ===== SECTIONS ===== */
        .section {
            margin-top: 1.6mm;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 6.8pt;
            font-weight: bold;
            letter-spacing: 0.9px;
            line-height: 9pt;
            text-transform: uppercase;
            color: #8B6F3E;
            border-bottom: 0.5pt solid #D9CFB8;
            padding-bottom: 0.8mm;
            margin-bottom: 1.2mm;
        }

        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            line-height: 10.2pt;
            color: #111111;
        }

        table.meta-table td {
            padding: 0.3mm 0;
            vertical-align: top;
            text-align: left;
            border-bottom: 0.4pt solid #F0EBE0;
        }

        table.meta-table tr:last-child td {
            border-bottom: 0;
        }

        table.meta-table.student-meta td.meta-label {
            width: 42mm;
            padding-right: 3mm;
            white-space: nowrap;
            color: #5A5348;
        }

        table.meta-table.course-meta td.meta-label {
            width: 66mm;
            padding-right: 3mm;
            white-space: nowrap;
            color: #5A5348;
        }

        table.meta-table td.meta-value {
            color: #111111;
        }

        .student-name {
            font-weight: bold;
            font-size: 10.5pt;
        }

        .student-name-gap {
            display: inline-block;
            width: 0.5em;
        }

        .meta-subnote {
            color: #5A5348;
            margin-left: 0.5em;
        }

        /* ===== ATTENDANCE TABLE ===== */
        .attendance-section {
            margin-top: 1.6mm;
            page-break-inside: avoid;
        }

        table.attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.8pt;
            line-height: 9.6pt;
        }

        table.attendance-table.compact {
            font-size: 7.2pt;
        }

        table.attendance-table.very-compact {
            font-size: 6.6pt;
        }

        table.attendance-table th,
        table.attendance-table td {
            border: 0.5pt solid #C9BFA8;
            padding: 0.55mm 0.7mm;
            text-align: center;
            vertical-align: middle;
            line-height: 9.4pt;
            color: #222222;
        }

        table.attendance-table.very-compact th,
        table.attendance-table.very-compact td {
            padding: 0.8mm 0.35mm;
            line-height: 8.6pt;
        }

        table.attendance-table thead th {
            background-color: #F5F0E4;
            color: #4A4235;
            font-weight: bold;
        }

        table.attendance-table td.row-label {
            text-align: left;
            padding-left: 1.5mm;
            white-space: nowrap;
            width: 31%;
            background-color: #FBF8F1;
            color: #4A4235;
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

        table.attendance-table td.sum {
            font-weight: bold;
            background-color: #FBF8F1;
        }

        .holidays-line {
            margin-top: 1.4mm;
            font-size: 7.8pt;
            line-height: 10.4pt;
            color: #222222;
        }

        .holidays-label {
            color: #5A5348;
        }

        /* ===== INSTRUCTORS & GOLD SEAL ===== */
        .auth-section {
            margin-top: 1.6mm;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 10mm;
        }

        .signature-line {
            width: 52mm;
            height: 0;
            border-top: 0.6pt solid #4A4235;
            margin: 0 auto 0.9mm;
        }

        table.auth-row {
            width: 100%;
            border-collapse: collapse;
        }

        table.auth-row td {
            vertical-align: middle;
            text-align: center;
            padding-top: 0.5mm;
        }

        td.instructor-col {
            width: 37%;
            padding-left: 2mm;
            padding-right: 2mm;
        }

        td.seal-col {
            width: 26%;
            padding-left: 1mm;
            padding-right: 1mm;
        }

        .seal-wrap {
            height: 20mm;
            margin: 0 auto;
            text-align: center;
            font-size: 0;
            line-height: 0;
        }

        .seal-img {
            width: auto;
            height: auto;
            max-width: 36mm;
            max-height: 20mm;
            display: block;
            margin: 0 auto;
        }

        .instructor-name {
            font-size: 8.8pt;
            font-weight: bold;
            color: #111111;
            line-height: 11pt;
        }

        .instructor-title {
            font-size: 7.6pt;
            color: #4A4235;
            margin-top: 0.4mm;
            line-height: 10pt;
        }

        .instructor-mail {
            font-size: 7.2pt;
            color: #4A4235;
            margin-top: 0.4mm;
            line-height: 9.8pt;
        }

        .mail-link {
            color: #1D4ED8;
            text-decoration: underline;
        }

        /* ===== FOOTER & QR VERIFICATION ===== */
        table.footer-row {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.2mm;
            border-top: 0.5pt solid #D9CFB8;
            page-break-inside: avoid;
        }

        table.footer-row td {
            padding-top: 1mm;
            vertical-align: bottom;
        }

        td.footer-spacer {
            width: 26mm;
        }

        td.footer-center {
            text-align: center;
            font-size: 7.8pt;
            line-height: 10.4pt;
            color: #222222;
        }

        td.footer-qr {
            width: 22mm;
            padding-right: 4mm;
            padding-top: 3mm;
            text-align: center;
        }

        .qr-box {
            width: 13mm;
            height: 13mm;
            padding: 0.5mm;
            margin: 0 auto 0.5mm;
            border: 0.5pt solid #C9BFA8;
            background-color: #FFFFFF;
            font-size: 0;
            line-height: 0;
        }

        .qr-img {
            width: 13mm;
            height: 13mm;
            display: block;
        }

        .qr-placeholder {
            width: 13mm;
            height: 13mm;
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
    <?php if(!empty($images['background'])): ?>
    <img class="bg" src="<?php echo e($images['background']); ?>" alt="">
    <?php endif; ?>

    <?php if($draft): ?>
    <div class="draft-overlay">DRAFT / 下書き</div>
    <?php endif; ?>

    <?php
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
    $classDaysJa = implode('、', $jaDays);
    $classDaysEn = $formatList($enDays, ' and ');

    $monthCount = $c->attendances->count();
    $tableSizeClass = $monthCount > 9 ? 'very-compact' : ($monthCount > 6 ? 'compact' : '');
    $totalClassHours = (int) $c->attendances->sum('class_hours');
    $totalAttendanceHours = (int) $c->attendances->sum('attendance_hours');
    $totalAttendanceRate = $totalClassHours > 0
    ? (int) round($totalAttendanceHours / $totalClassHours * 100)
    : 0;

    $contextJa = implode('、', array_filter($c->course->learningContextJapanese()));
    $contextEn = implode(', ', array_filter($c->course->learningContextEnglish()));
    $stackLearningContext = $contextJa !== '' && $contextEn !== '' && (mb_strlen($contextJa) + mb_strlen($contextEn)) > 45;

    $instructors = $c->instructors->values();
    $leftInstructor = $instructors->get(0);
    $rightInstructor = $instructors->get(1);
    ?>

    <div class="page">
        
        <div class="header-block">
            <?php if(!empty($images['logo'])): ?>
            <div class="header-logo">
                <img src="<?php echo e($images['logo']); ?>" alt="<?php echo e($school['name']); ?>">
            </div>
            <?php endif; ?>
            <div class="institution-name"><?php echo e($school['name']); ?></div>
            <div class="cert-title-ja">日本語学習修了証明書</div>
            <div class="cert-title-en">JAPANESE LANGUAGE COURSE COMPLETION CERTIFICATE</div>
            <div class="gold-divider-wrap">
                <?php if(!empty($images['divider'])): ?>
                <img class="gold-divider-img" src="<?php echo e($images['divider']); ?>" alt="">
                <?php else: ?>
                <div class="gold-divider-fallback"></div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="statement-block">
            <div class="ja">下記の者は、本校において日本語学習課程を修了したことをここに証明します。</div>
            <div class="en">This is to certify that the following student has successfully completed the Japanese language course at our institution.</div>
        </div>

        
        <div class="section">
            <div class="section-title">学生情報 / Student Information</div>
            <table class="meta-table student-meta">
                <tr>
                    <td class="meta-label">学生氏名 / Name:</td>
                    <td class="meta-value">
                        <span class="student-name"><?php echo e($c->student_name_en); ?></span><?php if($c->student_name_ja): ?><span class="student-name-gap"></span>（<?php echo e($c->student_name_ja); ?>）<?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="meta-label">生年月日 / Date of Birth:</td>
                    <td class="meta-value"><?php echo e($c->student_date_of_birth ? \App\Support\JapaneseDate::full($c->student_date_of_birth) : '—'); ?></td>
                </tr>
                <tr>
                    <td class="meta-label">性別 / Sex:</td>
                    <td class="meta-value"><?php echo e($c->studentGenderBilingual()); ?></td>
                </tr>
                <tr>
                    <td class="meta-label">学習期間 / Study Period:</td>
                    <td class="meta-value"><?php echo e(($c->study_start && $c->study_end) ? \App\Support\JapaneseDate::range($c->study_start, $c->study_end) : '—'); ?></td>
                </tr>
                <tr>
                    <td class="meta-label">学習時間 / Learning Time:</td>
                    <td class="meta-value">
                        1日<?php echo e($c->hours_per_day); ?>時間 週<?php echo e($c->days_per_week); ?>日 / <?php echo e($c->hours_per_day); ?> hours a day <?php echo e($c->days_per_week); ?> days per week
                        <?php if($classDaysJa !== '' || $classDaysEn !== ''): ?>
                        <br><?php echo e($classDaysJa); ?> / <?php echo e($classDaysEn); ?>

                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        
        <div class="section attendance-section">
            <div class="section-title">出席記録 / Attendance Record</div>
            <table class="attendance-table <?php echo e($tableSizeClass); ?>">
                <thead>
                    <tr>
                        <th class="row-label"></th>
                        <?php $__currentLoopData = $c->attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e(\App\Support\JapaneseDate::month($a->month)); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="sum-col">合計 (Total)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="row-label">授業時間 / Class Hour</td>
                        <?php $__currentLoopData = $c->attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td><?php echo e($a->class_hours); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="sum"><?php echo e($totalClassHours); ?></td>
                    </tr>
                    <tr>
                        <td class="row-label">出席時間 / Attendance Hour</td>
                        <?php $__currentLoopData = $c->attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td><?php echo e($a->attendance_hours); ?></td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="sum"><?php echo e($totalAttendanceHours); ?></td>
                    </tr>
                    <tr>
                        <td class="row-label">出席率 / Attendance Rate (%)</td>
                        <?php $__currentLoopData = $c->attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td><?php echo e($a->attendanceRate()); ?>%</td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="sum"><?php echo e($totalAttendanceRate); ?>%</td>
                    </tr>
                </tbody>
            </table>
            <div class="holidays-line">
                <span class="holidays-label">★休校日・祝日 / School &amp; National Holidays:</span> <?php echo e($c->national_holidays ?: '—'); ?>

            </div>
        </div>

        
        <div class="section">
            <div class="section-title">履修情報 / Course Details</div>
            <table class="meta-table course-meta">
                <tr>
                    <td class="meta-label">履修内容 / Learning Context:</td>
                    <td class="meta-value">
                        <?php if($stackLearningContext): ?>
                        <?php echo e($contextJa); ?><br><?php echo e($contextEn); ?>

                        <?php elseif($contextJa !== '' && $contextEn !== ''): ?>
                        <?php echo e($contextJa); ?> / <?php echo e($contextEn); ?>

                        <?php else: ?>
                        <?php echo e($contextJa ?: $contextEn); ?>

                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="meta-label">コース名称 / Course Name:</td>
                    <td class="meta-value"><?php echo e($c->course->name_ja); ?> / <?php echo e($c->course->name_en); ?></td>
                </tr>
                <tr>
                    <td class="meta-label">目標総時間 / Course Target Hours:</td>
                    <td class="meta-value"><?php echo e($c->course->total_hours ?? '—'); ?> 時間 / <?php echo e($c->course->total_hours ?? '—'); ?> Hours</td>
                </tr>
                <tr>
                    <td class="meta-label">実履修時間 / Actual Completed Hours:</td>
                    <td class="meta-value">
                        <?php echo e($totalClassHours); ?> 時間 / <?php echo e($totalClassHours); ?> Hours
                        <?php if($c->study_start && $c->study_end): ?>
                        <span class="meta-subnote">　(<?php echo e(\App\Support\JapaneseDate::range($c->study_start, $c->study_end)); ?>)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="meta-label">当該コース修了目標レベル / Learning Aim Level:</td>
                    <td class="meta-value"><?php echo e($c->course->aim_level_ja); ?><?php if($c->course->aim_level_en !== $c->course->aim_level_ja): ?> / <?php echo e($c->course->aim_level_en); ?><?php endif; ?></td>
                </tr>
                <tr>
                    <td class="meta-label">使用教材 / Book Name:</td>
                    <td class="meta-value"><?php echo e($c->course->book_ja); ?> / <?php echo e($c->course->book_en); ?></td>
                </tr>
                <?php if($c->issued_on): ?>
                <tr>
                    <td class="meta-label">発行日 / Date of Issue:</td>
                    <td class="meta-value"><?php echo e(\App\Support\JapaneseDate::full($c->issued_on)); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        
        <div class="section auth-section">
            <div class="section-title">署名 / Signatures and Seal</div>
            <table class="auth-row">
                <tr>
                    <td class="instructor-col">
                        <?php if($leftInstructor): ?>
                        <div class="signature-space"></div>
                        <div class="signature-line"></div>
                        <div class="instructor-name"><?php echo e($leftInstructor->name); ?></div>
                        <div class="instructor-title"><?php echo e($leftInstructor->title); ?></div>
                        <?php if($leftInstructor->email): ?>
                        <div class="instructor-mail">メール / Mail: <span class="mail-link"><?php echo e($leftInstructor->email); ?></span></div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td class="seal-col">
                        <?php if(!empty($images['seal'])): ?>
                        <div class="seal-wrap">
                            <img class="seal-img" src="<?php echo e($images['seal']); ?>" alt="Official School Seal">
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="instructor-col">
                        <?php if($rightInstructor): ?>
                        <div class="signature-space"></div>
                        <div class="signature-line"></div>
                        <div class="instructor-name"><?php echo e($rightInstructor->name); ?></div>
                        <div class="instructor-title"><?php echo e($rightInstructor->title); ?></div>
                        <?php if($rightInstructor->email): ?>
                        <div class="instructor-mail">メール / Mail: <span class="mail-link"><?php echo e($rightInstructor->email); ?></span></div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        
        <table class="footer-row">
            <tr>
                <td class="footer-spacer"></td>
                <td class="footer-center">
                    学校名 / School Name: <?php echo e($school['name']); ?><br>
                    住所 / Address: <?php echo e($school['address']); ?><br>
                    電話 / TEL: <?php echo e($school['phone']); ?>

                </td>
                <td class="footer-qr">
                    <?php if($qrBase64): ?>
                    <div class="qr-box">
                        <img class="qr-img" src="data:image/svg+xml;base64,<?php echo e($qrBase64); ?>" alt="QR Code">
                    </div>
                    <?php else: ?>
                    <div class="qr-placeholder"></div>
                    <?php endif; ?>
                    <?php if($c->certificate_no): ?>
                    <div class="qr-cert-no"><?php echo e($c->certificate_no); ?></div>
                    <?php endif; ?>
                    <div class="qr-caption">Scan to verify</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/certificates/pdf.blade.php ENDPATH**/ ?>