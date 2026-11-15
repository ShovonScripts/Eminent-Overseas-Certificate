<?php $__env->startSection('title', 'Certificate Verified — ' . $certificate->certificate_no); ?>

<?php $__env->startSection('content'); ?>
<div class="credential-dossier-card">
    <!-- Official Print Header (Visible strictly when printing on paper / PDF) -->
    <div class="print-only print-header">
        <div class="print-logo-wrap">
            <img src="<?php echo e(asset($school['images']['logo'])); ?>" alt="<?php echo e($school['name']); ?>">
        </div>
        <div class="print-header-titles">
            <h2><?php echo e($school['name']); ?></h2>
            <div class="print-subhead">OFFICIAL CREDENTIAL VERIFICATION RECORD &middot; 公的修了証明書 認証確認書</div>
            <div class="print-meta-line"><?php echo e($school['address']); ?> &middot; Tel: <?php echo e($school['phone']); ?> &middot; Web: <?php echo e($school['website']); ?></div>
        </div>
    </div>

    <!-- Official Registry Record Bar (Visible strictly in print) -->
    <div class="print-only print-record-bar">
        <div>
            <span class="rec-meta">CERTIFICATE NO: </span>
            <span class="rec-no"><?php echo e($certificate->certificate_no); ?></span>
        </div>
        <div>
            <span class="rec-status">OFFICIALLY REGISTERED &amp; GENUINE (有効)</span>
        </div>
        <div class="rec-meta">
            <span>ISSUED: <?php echo e($certificate->issued_on ? $certificate->issued_on->format('Y-m-d') : '—'); ?></span> &middot;
            <span>RECORD ID: <?php echo e($code); ?></span>
        </div>
    </div>

    <!-- Verification Status Banner (Screen Only) -->
    <div class="screen-only verification-status-bar is-valid">
        <div class="status-badge-group">
            <div class="status-icon-circle" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <div class="status-title-wrap">
                <h1>Official Certificate Verified</h1>
                <p>This Japanese language study credential has been authenticated as genuine and registered with <?php echo e($school['name']); ?>.</p>
            </div>
        </div>
        <div class="status-meta-pill">
            <div class="status-record-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span>Officially Registered</span>
            </div>
            <div class="status-verified-timestamp">Record ID: <?php echo e($code); ?></div>
        </div>
    </div>

    <!-- Dossier Body with Watermark -->
    <div class="dossier-body">
        <div class="dossier-watermark" style="background-image: url('<?php echo e(asset($school['images']['seal'] ?? 'images/certificate/seal.png')); ?>');" aria-hidden="true"></div>

        <!-- Certificate Reference Box (Screen Only) -->
        <div class="screen-only cert-ref-header">
            <div class="cert-no-block">
                <span class="cert-no-label">Certificate Registration Number (証明書番号)</span>
                <div class="cert-no-value-row">
                    <span class="cert-no-pill mono-val"><?php echo e($certificate->certificate_no); ?></span>
                    <button type="button" class="btn-copy-chip" onclick="copyText('<?php echo e($certificate->certificate_no); ?>', 'Certificate Number')" title="Copy Certificate Number">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                        </svg>
                        <span>Copy</span>
                    </button>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                <span class="cert-status-tag is-active">Active &amp; Genuine (有効)</span>
                <span style="font-size: 11.5px; color: var(--color-gov-muted); font-weight: 600;">Issued On: <?php echo e($certificate->issued_on ? $certificate->issued_on->format('Y-m-d') : '—'); ?></span>
            </div>
        </div>

        <!-- Section I: Student Identification Record -->
        <div class="dossier-section">
            <div class="dossier-section-title">
                <span class="section-roman">I.</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Student Identification Record (受講生基本情報)</span>
            </div>
            <div class="dossier-grid">
                <div class="dossier-item span-2">
                    <span class="dossier-label">Student Legal Name (氏名)</span>
                    <div class="dossier-value is-hero-name">
                        <span><?php echo e($certificate->student_name_en); ?></span>
                        <?php if($certificate->student_name_ja): ?>
                            <span class="katakana-pill ja-tag" title="Japanese Katakana Transcription"><?php echo e($certificate->student_name_ja); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dossier-item">
                    <span class="dossier-label">Gender (性別)</span>
                    <span class="dossier-value"><?php echo e($certificate->student_gender ? ucfirst($certificate->student_gender->value) : '—'); ?></span>
                </div>

                <div class="dossier-item">
                    <span class="dossier-label">Data Privacy Status (個人情報保護)</span>
                    <span class="dossier-value" style="color: var(--color-verified-forest); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span>Sealed for Consular Privacy (個人情報保護法準拠)</span>
                    </span>
                </div>
            </div>

            <div class="privacy-sealed-notice screen-only">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>In compliance with personal data protection regulations and consular privacy standards, confidential student contact records (date of birth, residential address, telephone, email) are kept strictly confidential and omitted from the public verification registry.</span>
            </div>
        </div>

        <!-- Section II: Academic Curriculum & Attendance Ledger -->
        <div class="dossier-section">
            <div class="dossier-section-title">
                <span class="section-roman">II.</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
                <span>Academic Curriculum &amp; Attendance Ledger (履修課程及び出席記録)</span>
            </div>

            <div class="dossier-grid" style="margin-bottom: 14px;">
                <div class="dossier-item span-2">
                    <span class="dossier-label">Course Program (受講コース)</span>
                    <div class="dossier-value">
                        <strong style="font-size: 15px; color: var(--color-gov-navy);"><?php echo e($certificate->course->name_en); ?></strong>
                        <?php if($certificate->course->name_ja): ?>
                            <span style="color: var(--color-gov-muted); font-size: 13.5px; margin-left: 6px;">/ <?php echo e($certificate->course->name_ja); ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top: 3px;">
                        <span class="level-badge">Level: <?php echo e($certificate->course->level_code); ?></span>
                        <?php if($certificate->course->aim_level_en): ?>
                            <span style="font-size: 12px; color: var(--color-gov-muted); margin-left: 6px;">Target: <?php echo e($certificate->course->aim_level_en); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dossier-item">
                    <span class="dossier-label">Study Period (就学期間)</span>
                    <span class="dossier-value">
                        <?php echo e($certificate->study_start ? $certificate->study_start->format('Y-m-d') : '—'); ?> &rarr; <?php echo e($certificate->study_end ? $certificate->study_end->format('Y-m-d') : '—'); ?>

                    </span>
                </div>

                <div class="dossier-item">
                    <span class="dossier-label">Class Schedule (受講形態)</span>
                    <span class="dossier-value">
                        <?php echo e($certificate->days_per_week); ?> Days / Wk &middot; <?php echo e($certificate->hours_per_day); ?> Hours / Session
                    </span>
                </div>
            </div>

            <!-- Formal Academic Attendance Ledger Table -->
            <table class="formal-ledger-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Curriculum Standard (MOJ)</th>
                        <th style="width: 25%;">Classroom Presence Attended</th>
                        <th style="width: 25%;">Certified Attendance Rate</th>
                        <th style="width: 25%;">Academic Standing</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="ledger-num"><?php echo e($certificate->total_class_hours); ?> <span class="ledger-unit">Hours</span></div>
                            <div class="ledger-sub">Prescribed Syllabus</div>
                        </td>
                        <td>
                            <div class="ledger-num"><?php echo e($certificate->total_attendance_hours); ?> <span class="ledger-unit">Hours</span></div>
                            <div class="ledger-sub">Direct Classroom Presence</div>
                        </td>
                        <td>
                            <div class="ledger-num"><?php echo e(round((float) $certificate->attendance_rate)); ?>%</div>
                            <div class="ledger-sub">Certified Attendance</div>
                        </td>
                        <td>
                            <div>
                                <span class="ledger-status-badge">Completed &amp; Certified</span>
                            </div>
                            <div class="ledger-sub">課程修了認定</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section III: Issuing Authority & Cryptographic Verification -->
        <div class="dossier-section">
            <div class="dossier-section-title">
                <span class="section-roman">III.</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"/>
                    <path d="M5 21V10"/>
                    <path d="M19 21V10"/>
                    <path d="M9 21V10"/>
                    <path d="M15 21V10"/>
                    <path d="M2 10l10-7 10 7"/>
                    <path d="M12 4v3"/>
                </svg>
                <span>Issuing Authority &amp; Registration Security (発行機関及び電子検証)</span>
            </div>

            <div class="dossier-grid">
                <div class="dossier-item">
                    <span class="dossier-label">Issuing Authority (発行機関)</span>
                    <span class="dossier-value" style="font-weight: 700;"><?php echo e($school['name']); ?></span>
                </div>

                <div class="dossier-item">
                    <span class="dossier-label">Institutional Status (機関種別)</span>
                    <span class="dossier-value">Japanese Language Training Institute (日本語教育機関)</span>
                </div>

                <div class="dossier-item span-2">
                    <span class="dossier-label">Campus Location (所在地)</span>
                    <span class="dossier-value"><?php echo e($school['address']); ?></span>
                </div>

                <?php if($certificate->instructors && $certificate->instructors->isNotEmpty()): ?>
                    <div class="dossier-item span-2">
                        <span class="dossier-label">Certified Japanese Faculty (担当教員)</span>
                        <span class="dossier-value"><?php echo e($certificate->instructors->pluck('name')->join(', ')); ?></span>
                    </div>
                <?php endif; ?>

                <?php if($certificate->pdf_sha256): ?>
                    <div class="dossier-item span-2">
                        <span class="dossier-label">Cryptographic PDF SHA-256 Digest (改ざん検知コード)</span>
                        <div class="sha256-box">
                            <span class="sha256-hash"><?php echo e($certificate->pdf_sha256); ?></span>
                            <button type="button" class="btn-copy-chip screen-only" onclick="copyText('<?php echo e($certificate->pdf_sha256); ?>', 'SHA-256 Hash')" title="Copy SHA-256 Digest">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                    <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                </svg>
                                <span>Copy</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formal Consular Certification & Institutional Seal Block -->
        <div class="formal-auth-block">
            <div class="formal-auth-clause">
                <strong>Official Registry Verification Attestation (公式認証証明):</strong>
                This verification statement certifies that the educational credentials and attendance record presented above have been verified directly against the archival database of <strong><?php echo e($school['name']); ?></strong>. Certified genuine and valid for consular review, embassy visa verification, and the Immigration Services Agency of Japan (出入国在留管理庁).
                <div class="auth-sign-meta">
                    <span>Authorized by: Office of the Registrar / Academic Affairs</span>
                    <span>Registry Verification Date: <?php echo e(now()->format('Y-m-d')); ?></span>
                    <span>Audit Record ID: <strong style="font-family: var(--font-mono);"><?php echo e($code); ?></strong></span>
                </div>
            </div>
            <div class="formal-stamp-seal" title="Official Institutional Seal">
                <svg viewBox="0 0 100 100" width="100" height="100">
                    <circle cx="50" cy="50" r="47" fill="#FFFFFF" stroke="#0A192F" stroke-width="1.8" stroke-dasharray="3,2"/>
                    <circle cx="50" cy="50" r="43" fill="none" stroke="#0A192F" stroke-width="1"/>
                    <circle cx="50" cy="50" r="28" fill="none" stroke="#0A192F" stroke-width="0.8"/>
                    <path id="stampArcTop" d="M 16,50 A 34,34 0 0,1 84,50" fill="none"/>
                    <path id="stampArcBot" d="M 84,50 A 34,34 0 0,1 16,50" fill="none"/>
                    <text font-size="5.8" font-family="'Cinzel', serif" font-weight="800" fill="#0A192F" letter-spacing="1">
                        <textPath href="#stampArcTop" startOffset="50%" text-anchor="middle">EMINENT OVERSEAS</textPath>
                    </text>
                    <text font-size="5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="700" fill="#475569" letter-spacing="1">
                        <textPath href="#stampArcBot" startOffset="50%" text-anchor="middle">&middot; VERIFIED &amp; SEALED &middot;</textPath>
                    </text>
                    <text x="50" y="44" font-size="5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="800" fill="#0A192F" text-anchor="middle" letter-spacing="0.5">REGISTRAR</text>
                    <line x1="33" y1="46.5" x2="67" y2="46.5" stroke="#0A192F" stroke-width="0.75"/>
                    <text x="50" y="52" font-size="6.5" font-family="'Plus Jakarta Sans', sans-serif" font-weight="900" fill="#065F46" text-anchor="middle" letter-spacing="0.5">AUTHENTIC</text>
                    <line x1="33" y1="54.5" x2="67" y2="54.5" stroke="#0A192F" stroke-width="0.75"/>
                    <text x="50" y="60" font-size="4.2" font-family="'Plus Jakarta Sans', sans-serif" font-weight="700" fill="#64748B" text-anchor="middle" letter-spacing="0.5">OFFICIAL SEAL</text>
                </svg>
            </div>
        </div>

        <!-- Dossier Action Buttons (Screen Only) -->
        <div class="dossier-actions screen-only">
            <div class="dossier-actions-left">
                <!-- Visit Official Website: pulls link dynamically from /admin/settings ($school['website']) -->
                <a href="<?php echo e($school['website'] ?? config('school.website', 'https://eminentoverseas.uk')); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn-action btn-website"
                   title="Visit the official school website">
                    <span>Visit Official Website</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                </a>

                <!-- Print Verification Sheet for Embassy / Visa Applications -->
                <button type="button" onclick="window.print()" class="btn-action btn-print" title="Print this official verification record">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect width="12" height="8" x="6" y="14"/>
                    </svg>
                    <span>Print Verification Sheet</span>
                </button>
            </div>

            <!-- Verify Another Credential Link -->
            <a href="<?php echo e(route('verify.form')); ?>" class="btn-action btn-verify-more" title="Verify another certificate">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Verify Another Certificate</span>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('verify.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/verify/valid.blade.php ENDPATH**/ ?>