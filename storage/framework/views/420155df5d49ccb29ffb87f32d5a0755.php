<?php $__env->startSection('title', 'Certificate Verified — ' . $certificate->certificate_no); ?>

<?php $__env->startSection('styles'); ?>
    <style>
        .valid-card .status-banner {
            background: linear-gradient(135deg, #064E3B 0%, #065F46 100%);
            color: #fff;
            padding: 24px;
        }
        .valid-card .status-banner h1 { font-size: 20px; font-weight: 800; margin: 0 0 4px; }
        .valid-card .status-banner p { font-size: 13px; opacity: 0.9; margin: 0; }
        .valid-card .card-body { padding: 28px 32px; }
        
        .valid-card .cert-ref {
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            padding: 16px 20px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px;
            margin-bottom: 24px;
        }
        .valid-card .cert-no { font-family: var(--font-mono); font-size: 18px; font-weight: 800; color: #0A192F; }
        .valid-card .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase;
            border-radius: 9999px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;
        }
        .valid-card .cert-ref time { font-size: 12px; color: #64748B; font-weight: 600; }

        .valid-card .section { margin-bottom: 24px; }
        .valid-card .section h2 {
            font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
            color: #0A192F; display: flex; align-items: center; gap: 10px;
            margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1.5px solid #E2E8F0;
        }
        .valid-card .section-num {
            min-width: 24px; height: 22px; display: inline-flex; align-items: center; justify-content: center;
            padding: 0 6px; border-radius: 4px; background: #F1F5F9; color: #0A192F;
            font-family: var(--font-serif); font-weight: 700; font-size: 11px; border: 1px solid #E2E8F0;
        }

        .valid-card .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px 24px; }
        .valid-card .item { display: flex; flex-direction: column; gap: 4px; }
        .valid-card .item.span-2 { grid-column: span 2; }
        .valid-card .item dt { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748B; }
        .valid-card .item dd { font-size: 15px; font-weight: 600; color: #0A192F; line-height: 1.4; }
        .valid-card .name { font-size: 19px; font-weight: 800; letter-spacing: -0.01em; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .valid-card .ja-tag { font-size: 13px; font-weight: 600; color: #334155; background: #F1F5F9; border: 1px solid #E2E8F0; border-radius: 6px; padding: 2px 10px; }
        .valid-card .level-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; font-size: 12px; font-weight: 700; color: #1E40AF; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 4px; margin-top: 4px; }

        .valid-card .attendance-table {
            width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 13px;
            background: #fff; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden;
        }
        .valid-card .attendance-table th {
            background: #F8FAFC; color: #334155; font-size: 10.5px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; border: 1px solid #E2E8F0; text-align: center;
        }
        .valid-card .attendance-table td { padding: 14px 16px; border: 1px solid #E2E8F0; text-align: center; }
        .valid-card .attendance-table .num { font-size: 19px; font-weight: 800; font-family: var(--font-mono); color: #0A192F; }
        .valid-card .attendance-table .badge { font-size: 11px; padding: 3px 10px; }

        .valid-card .attestation {
            display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: center;
            padding: 20px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; margin-top: 8px;
        }
        .valid-card .attestation-text { font-size: 12px; color: #475569; line-height: 1.6; }
        .valid-card .attestation-text strong { color: #0A192F; }
        .valid-card .attestation-meta { margin-top: 10px; padding-top: 8px; border-top: 1px dashed #E2E8F0; font-size: 11px; color: #64748B; display: flex; gap: 16px; flex-wrap: wrap; }
        .valid-card .attestation-meta code { font-family: var(--font-mono); font-size: 10.5px; }
        .valid-card .seal { width: 90px; height: 90px; flex-shrink: 0; }

        .valid-card .privacy-notice {
            display: flex; align-items: flex-start; gap: 10px; background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 8px; padding: 12px 16px; margin-top: 16px; font-size: 11.5px; color: #64748B; line-height: 1.5;
        }

        .valid-card .actions {
            margin-top: 28px; padding-top: 20px; border-top: 1px solid #E2E8F0;
            display: flex; gap: 12px; flex-wrap: wrap;
        }
        .valid-card .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 22px; font-size: 13.5px; font-weight: 700; border-radius: 8px;
            text-decoration: none; cursor: pointer; transition: all 0.15s ease; border: none;
        }
        .valid-card .btn svg { width: 14px; height: 14px; }
        .valid-card .btn-primary { background: #0A192F; color: #fff; }
        .valid-card .btn-primary:hover { background: #172A45; }
        .valid-card .btn-secondary { background: #fff; color: #0A192F; border: 1px solid #E2E8F0; }
        .valid-card .btn-secondary:hover { background: #F8FAFC; border-color: #CBD5E1; }
        .valid-card .btn-ghost { background: transparent; color: #475569; }
        .valid-card .btn-ghost:hover { background: #F1F5F9; color: #0A192F; }

        .valid-card .sha256 { margin-top: 8px; }
        .valid-card .sha256 summary {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            cursor: pointer; user-select: none; padding: 4px 0; font-size: 12px; font-weight: 600; color: #334155;
        }
        .valid-card .sha256 summary svg { color: #64748B; }
        .valid-card .sha256 summary::-webkit-details-marker { display: none; }
        .valid-card .sha256-box {
            background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 14px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 8px;
        }
        .valid-card .sha256-box code { font-family: var(--font-mono); font-size: 11px; color: #475569; word-break: break-all; flex: 1; }
        .valid-card .sha256 .btn-copy {
            background: #fff; border: 1px solid #E2E8F0; border-radius: 6px; padding: 4px 10px;
            font-size: 10.5px; font-weight: 600; color: #475569; cursor: pointer;
            display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease;
        }
        .valid-card .sha256 .btn-copy:hover { border-color: #94A3B8; color: #0A192F; }

        @media (max-width: 680px) {
            .valid-card .card-body { padding: 20px; }
            .valid-card .status-banner { padding: 20px; }
            .valid-card .status-banner h1 { font-size: 17px; }
            .valid-card .info-grid { grid-template-columns: 1fr; }
            .valid-card .item.span-2 { grid-column: auto; }
            .valid-card .cert-ref { flex-direction: column; align-items: flex-start; gap: 12px; }
            .valid-card .attestation { grid-template-columns: 1fr; text-align: center; }
            .valid-card .seal { margin: 0 auto; }
            .valid-card .actions { flex-direction: column; }
            .valid-card .btn { width: 100%; }
        }

        @media print {
            .gov-header, .gov-footer, .actions, .btn-copy, .sha256 summary, .sha256 .btn-copy,
            .privacy-notice, .status-banner, .cert-ref, .watermark { display: none !important; }
            .print-only { display: block !important; }
            .valid-card { border: none !important; box-shadow: none !important; }
            .valid-card .card-body { padding: 0 !important; }
            .valid-card .section { page-break-inside: avoid; }
            .valid-card .attestation { page-break-inside: avoid; }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="credential-card valid-card">
    <!-- Print Header -->
    <div class="print-only print-header">
        <div class="print-logo-wrap">
            <img src="<?php echo e(asset($school['images']['logo'])); ?>" alt="<?php echo e($school['name']); ?>">
        </div>
        <div class="print-header-titles">
            <h2><?php echo e($school['name']); ?></h2>
            <div class="print-subhead">OFFICIAL CREDENTIAL VERIFICATION &middot; 公的修了証明書 認証確認</div>
            <div class="print-meta"><?php echo e($school['address']); ?> &middot; <?php echo e($school['phone']); ?> &middot; <?php echo e($school['website']); ?></div>
        </div>
    </div>

    <!-- Print Record Bar -->
    <div class="print-only print-record">
        <span class="rec-no"><?php echo e($certificate->certificate_no); ?></span>
        <span class="rec-status">OFFICIALLY REGISTERED & GENUINE (有効)</span>
        <span class="rec-meta">Issued: <?php echo e($certificate->issued_on?->format('Y-m-d')); ?> &middot; Record ID: <?php echo e($code); ?></span>
    </div>

    <!-- Status Banner -->
    <div class="screen-only status-banner">
        <h1>Certificate Verified</h1>
        <p>This Japanese language study credential is authentic and registered with <?php echo e($school['name']); ?>.</p>
    </div>

    <div class="card-body">
        <!-- Certificate Reference -->
        <div class="screen-only cert-ref">
            <div class="cert-no-block">
                <span class="cert-no-label">Certificate No. (証明書番号)</span>
                <span class="cert-no mono"><?php echo e($certificate->certificate_no); ?></span>
            </div>
            <div class="cert-status-row">
                <span class="badge">Active & Genuine (有効)</span>
                <time>Issued <?php echo e($certificate->issued_on?->format('Y-m-d')); ?></time>
            </div>
        </div>

        <!-- Student & Course -->
        <section class="section">
            <h2><span class="section-num">1</span><span>Student & Course</span></h2>
            <dl class="info-grid">
                <div class="item">
                    <dt>Student Name (氏名)</dt>
                    <dd class="name">
                        <?php echo e($certificate->student_name_en); ?>

                        <?php if($certificate->student_name_ja): ?>
                            <span class="ja-tag"><?php echo e($certificate->student_name_ja); ?></span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="item">
                    <dt>Gender (性別)</dt>
                    <dd><?php echo e($certificate->student_gender?->label()); ?></dd>
                </div>
                <div class="item">
                    <dt>Course (受講コース)</dt>
                    <dd>
                        <strong><?php echo e($certificate->course->name_en); ?></strong>
                        <?php if($certificate->course->name_ja): ?>
                            <span class="ja-tag">/ <?php echo e($certificate->course->name_ja); ?></span>
                        <?php endif; ?>
                        <span class="level-badge"><?php echo e($certificate->course->level_code); ?></span>
                    </dd>
                </div>
                <div class="item">
                    <dt>Study Period (就学期間)</dt>
                    <dd><?php echo e($certificate->study_start?->format('Y-m-d')); ?> &rarr; <?php echo e($certificate->study_end?->format('Y-m-d')); ?></dd>
                </div>
                <div class="item">
                    <dt>Schedule (受講形態)</dt>
                    <dd><?php echo e($certificate->days_per_week); ?> days/wk &middot; <?php echo e($certificate->hours_per_day); ?> h/session</dd>
                </div>
            </dl>

            <!-- Attendance Summary -->
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Curriculum (MOJ)</th>
                        <th>Attended</th>
                        <th>Rate</th>
                        <th>Standing</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="num"><?php echo e($certificate->total_class_hours); ?></span> Hours</td>
                        <td><span class="num"><?php echo e($certificate->total_attendance_hours); ?></span> Hours</td>
                        <td><span class="num"><?php echo e(round((float) $certificate->attendance_rate)); ?>%</span></td>
                        <td><span class="badge">Completed & Certified</span></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Issuing Authority & Verification -->
        <section class="section">
            <h2><span class="section-num">2</span><span>Issuing Authority & Verification</span></h2>
            <dl class="info-grid">
                <div class="item span-2">
                    <dt>Issuing Authority</dt>
                    <dd><strong><?php echo e($school['name']); ?></strong></dd>
                </div>
                <div class="item span-2">
                    <dt>Campus</dt>
                    <dd><?php echo e($school['address']); ?></dd>
                </div>
                <?php if($certificate->instructors && $certificate->instructors->isNotEmpty()): ?>
                    <div class="item span-2">
                        <dt>Faculty</dt>
                        <dd><?php echo e($certificate->instructors->pluck('name')->join(', ')); ?></dd>
                    </div>
                <?php endif; ?>
                <?php if($certificate->pdf_sha256): ?>
                    <details class="item span-2 sha256">
                        <summary>
                            PDF SHA-256
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </summary>
                        <div class="sha256-box">
                            <code><?php echo e($certificate->pdf_sha256); ?></code>
                            <button class="btn-copy" onclick="copyText('<?php echo e($certificate->pdf_sha256); ?>', 'SHA-256')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                <span>Copy</span>
                            </button>
                        </div>
                    </details>
                <?php endif; ?>
            </dl>
        </section>

        <!-- Attestation + Seal -->
        <div class="attestation">
            <div class="attestation-text">
                <strong>Official Registry Attestation:</strong>
                The above credentials are verified against the archival database of <strong><?php echo e($school['name']); ?></strong>.
                Valid for consular review, embassy visa, and Immigration Services Agency of Japan.
                <div class="attestation-meta">
                    <span>Authorized by: Office of the Registrar</span>
                    <span>Verified: <?php echo e(now()->format('Y-m-d')); ?></span>
                    <span>Audit ID: <code><?php echo e($code); ?></code></span>
                </div>
            </div>
            <div class="seal" title="Official Seal" aria-label="Official institutional seal">
                <svg viewBox="0 0 90 90" width="90" height="90" style="color: #000;">
                    <circle cx="45" cy="45" r="42" fill="white" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4,3"/>
                    <circle cx="45" cy="45" r="37" fill="none" stroke="currentColor" stroke-width="1"/>
                    <circle cx="45" cy="45" r="25" fill="none" stroke="currentColor" stroke-width="0.6"/>
                    <path id="arcTop" d="M 13,45 A 32,32 0 0,1 77,45" fill="none"/>
                    <path id="arcBot" d="M 77,45 A 32,32 0 0,1 13,45" fill="none"/>
                    <text font-size="5" font-family="'Cinzel',serif" font-weight="800" fill="currentColor" letter-spacing="0.8"><textPath href="#arcTop" startOffset="50%" text-anchor="middle">EMINENT OVERSEAS</textPath></text>
                    <text font-size="4.5" font-family="'Plus Jakarta Sans',sans-serif" font-weight="700" fill="currentColor" letter-spacing="0.8"><textPath href="#arcBot" startOffset="50%" text-anchor="middle">&middot; VERIFIED &middot;</textPath></text>
                    <text x="45" y="38" font-size="4.5" font-family="'Plus Jakarta Sans',sans-serif" font-weight="800" fill="currentColor" text-anchor="middle">REGISTRAR</text>
                    <line x1="28" y1="41" x2="62" y2="41" stroke="currentColor" stroke-width="0.6"/>
                    <text x="45" y="48" font-size="5.5" font-family="'Plus Jakarta Sans',sans-serif" font-weight="900" fill="currentColor" text-anchor="middle">AUTHENTIC</text>
                    <line x1="28" y1="51" x2="62" y2="51" stroke="currentColor" stroke-width="0.6"/>
                    <text x="45" y="57" font-size="3.5" font-family="'Plus Jakarta Sans',sans-serif" font-weight="700" fill="currentColor" text-anchor="middle">OFFICIAL SEAL</text>
                </svg>
            </div>
        </div>

        <!-- Print Footer Note -->
        <div class="print-only print-footer-note">
            This document is an official verification printout from the <?php echo e($school['name']); ?> Credential Registry.
            Valid for consular, embassy, and immigration authority review. Verify online at:
            <?php echo e(url('/')); ?>/<?php echo e($code); ?>

        </div>

        <!-- Privacy Notice -->
        <p class="privacy-notice">Personal contact data (date of birth, phone, email) is protected and omitted from this public record.</p>

        <!-- Actions -->
        <div class="actions screen-only">
            <a href="<?php echo e($school['website'] ?? 'https://eminentoverseas.uk'); ?>" target="_blank" rel="noopener" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>Official Website</span>
            </a>
            <button onclick="window.print()" class="btn btn-secondary" title="Print verification">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                <span>Print</span>
            </button>
            <a href="<?php echo e(route('verify.form')); ?>" class="btn btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span>Verify Another</span>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('verify.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/verify/valid.blade.php ENDPATH**/ ?>