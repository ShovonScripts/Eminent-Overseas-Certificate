<?php $__env->startSection('title', $certificate->certificate_no ?? 'Draft certificate'); ?>
<?php $__env->startSection('eyebrow', 'Certificate Details'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <?php if($certificate->isDraft()): ?>
        <a class="btn btn-secondary" href="<?php echo e(route('admin.certificates.edit', $certificate)); ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
            </svg>
            <span>Edit Draft</span>
        </a>
    <?php endif; ?>
    <a class="btn btn-secondary" href="<?php echo e(route('certificates.preview', $certificate)); ?>" target="_blank" rel="noopener">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span>Preview PDF</span>
    </a>
    <?php if(! $certificate->isDraft()): ?>
        <a class="btn btn-primary" href="<?php echo e(route('certificates.pdf', $certificate)); ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            <span>Download PDF</span>
        </a>
    <?php endif; ?>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.certificates.index')); ?>">
        &larr; Back to list
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if($certificate->isDraft()): ?>
        <div class="alert alert-warning">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <strong>Draft Certificate Notice:</strong> This certificate is currently a draft. It has no assigned certificate number, QR code or public verification page until it is formally issued.
            </div>
        </div>
    <?php elseif($certificate->isRevoked()): ?>
        <div class="alert alert-danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <div>
                <strong>Certificate Revoked:</strong> This certificate was revoked on <?php echo e($certificate->revoked_at?->format('Y-m-d H:i')); ?>.
                <?php if($certificate->revoke_reason): ?>
                    <div class="mt-1">Reason: <em><?php echo e($certificate->revoke_reason); ?></em></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Student & Course Summary</h2>
                    <p><?php echo e($certificate->course?->name_en); ?> &middot; <span class="badge badge-muted" style="font-size: 0.65rem;"><?php echo e($certificate->course?->level_code); ?></span></p>
                </div>
                <span class="badge badge-<?php echo e($certificate->status->value); ?>"><?php echo e($certificate->status->label()); ?></span>
            </div>
            <div class="card-body">
                <dl class="detail-grid">
                    <dt>Certificate No.</dt>
                    <dd>
                        <?php if($certificate->certificate_no): ?>
                            <span class="mono" style="font-size: 1rem; color: #1E3A8A; font-weight: 700;"><?php echo e($certificate->certificate_no); ?></span>
                        <?php else: ?>
                            <span class="text-muted">Not assigned yet (Draft)</span>
                        <?php endif; ?>
                    </dd>

                    <dt>Student</dt>
                    <dd>
                        <strong style="font-size: 1rem; color: #0F172A;"><?php echo e($certificate->student_name_en); ?></strong>
                        <?php if($certificate->student_name_ja): ?>
                            <div class="text-muted" style="font-size: 0.85rem;"><?php echo e($certificate->student_name_ja); ?></div>
                        <?php endif; ?>
                        <div class="text-muted mt-1" style="font-size: 0.75rem; line-height: 1.4;">
                            DOB: <strong><?php echo e($certificate->student_date_of_birth?->format('Y-m-d')); ?></strong> | 
                            Sex: <strong><?php echo e($certificate->student_gender?->label()); ?> / <?php echo e($certificate->student_gender?->japaneseLabel()); ?></strong>
                            <?php if($certificate->student_email): ?>
                                <br>Email: <?php echo e($certificate->student_email); ?>

                            <?php endif; ?>
                            <?php if($certificate->student_phone): ?>
                                | Phone: <?php echo e($certificate->student_phone); ?>

                            <?php endif; ?>
                        </div>
                    </dd>

                    <dt>Study Period</dt>
                    <dd>
                        <?php echo e($certificate->study_start?->format('Y-m-d')); ?> &rarr; <?php echo e($certificate->study_end?->format('Y-m-d')); ?>

                    </dd>

                    <dt>Schedule</dt>
                    <dd><?php echo e($certificate->hours_per_day); ?> hours/day, <?php echo e($certificate->days_per_week); ?> days/week</dd>

                    <dt>Class Days</dt>
                    <dd><?php echo e(implode(', ', $certificate->class_days ?? [])); ?></dd>

                    <dt>Other Holidays</dt>
                    <dd><?php echo e($certificate->national_holidays ?: '—'); ?></dd>

                    <dt>Issue Date</dt>
                    <dd><?php echo e($certificate->issued_on?->format('Y-m-d') ?? '—'); ?></dd>

                    <?php if($certificate->issued_at): ?>
                        <dt>Issued At</dt>
                        <dd>
                            <?php echo e($certificate->issued_at->format('Y-m-d H:i')); ?>

                            <?php if($certificate->issuedBy): ?>
                                <span class="text-muted">by <?php echo e($certificate->issuedBy->name); ?></span>
                            <?php endif; ?>
                        </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Public Verification Record</h2>
                    <p>Cryptographic QR identity parameters</p>
                </div>
            </div>
            <div class="card-body">
                <?php if($certificate->verification_code): ?>
                    <dl class="detail-grid">
                        <dt>Verification Code</dt>
                        <dd><span class="mono" style="font-weight: 700; color: #0F172A;"><?php echo e($certificate->verification_code); ?></span></dd>

                        <dt>Public URL</dt>
                        <dd>
                            <a href="<?php echo e($certificate->verifyUrl()); ?>" target="_blank" rel="noopener" style="font-size: 0.8125rem; word-break: break-all; display: inline-flex; align-items: center; gap: 4px;">
                                <span><?php echo e($certificate->verifyUrl()); ?></span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                        </dd>

                        <dt>PDF SHA-256</dt>
                        <dd class="mono" style="font-size: 0.75rem; word-break: break-all; color: #64748B;"><?php echo e($certificate->pdf_sha256 ?: '—'); ?></dd>
                    </dl>
                <?php else: ?>
                    <div style="background: #F8FAFC; padding: var(--space-5); border-radius: var(--radius-md); border: 1px dashed var(--color-border); text-align: center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 8px;">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <rect width="5" height="5" x="7" y="7"/>
                            <rect width="5" height="5" x="7" y="12"/>
                            <rect width="5" height="5" x="12" y="7"/>
                        </svg>
                        <p class="text-muted mb-0" style="font-size: 0.8125rem;">The verification code, QR code and public URL will be generated automatically when this certificate is issued.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h2>Monthly Attendance Hours</h2>
                <p><?php echo e($certificate->total_class_hours); ?> total class hours &middot; <?php echo e($certificate->total_attendance_hours); ?> attended &middot; <strong style="color: #059669;"><?php echo e(round((float) $certificate->attendance_rate)); ?>%</strong></p>
            </div>
        </div>
        <?php if($certificate->attendances->isEmpty()): ?>
            <div class="empty">
                <strong>No attendance breakdown recorded</strong>
                <p class="text-muted">Monthly attendance hours should be configured before issuing the certificate.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Month Period</th>
                        <th class="num">Class Hours</th>
                        <th class="num">Attended Hours</th>
                        <th class="num">Attendance Rate</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $certificate->attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong style="color: #0F172A;"><?php echo e($attendance->year); ?>-<?php echo e(str_pad((string) $attendance->month, 2, '0', STR_PAD_LEFT)); ?></strong></td>
                            <td class="num"><?php echo e($attendance->class_hours); ?> hrs</td>
                            <td class="num"><?php echo e($attendance->attendance_hours); ?> hrs</td>
                            <td class="num">
                                <span class="badge <?php echo e($attendance->attendanceRate() >= 80 ? 'badge-issued' : 'badge-warning'); ?>">
                                    <?php echo e($attendance->attendanceRate()); ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Instructor Signatures</h2>
                    <p>Printed on the bottom border of the certificate</p>
                </div>
            </div>
            <div class="card-body">
                <?php if($certificate->instructors->isEmpty()): ?>
                    <p class="text-muted mb-0">No instructors selected.</p>
                <?php else: ?>
                    <dl class="detail-grid">
                        <?php $__currentLoopData = $certificate->instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <dt><?php echo e($loop->first ? 'Primary Instructor' : 'Secondary Instructor'); ?></dt>
                            <dd>
                                <strong><?php echo e($instructor->name); ?></strong>
                                <span class="text-muted"> / <?php echo e($instructor->title); ?></span>
                            </dd>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </dl>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Issuing & Lifecycle Actions</h2>
                    <p>Status transitions and irreversible operations</p>
                </div>
            </div>
            <div class="card-body">
                <?php if($certificate->isDraft()): ?>
                    <form method="post" action="<?php echo e(route('admin.certificates.issue', $certificate)); ?>"
                          onsubmit="return confirm('Issue this certificate? A permanent certificate number, QR code and PDF will be generated, and the data becomes read-only.')">
                        <?php echo csrf_field(); ?>
                        <p class="text-muted" style="font-size: 0.8125rem;">Issuing generates the certificate number, unique verification code, renders the official PDF document, and locks the record against edits.</p>
                        <button class="btn btn-gold btn-lg" type="submit">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>Issue Certificate Now</span>
                        </button>
                    </form>
                <?php elseif($certificate->isIssued()): ?>
                    <?php if(auth()->user()?->isSuperAdmin()): ?>
                        <form method="post" action="<?php echo e(route('admin.certificates.revoke', $certificate)); ?>"
                              onsubmit="return confirm('Revoke this certificate? The public verification page will immediately show it as revoked.')">
                            <?php echo csrf_field(); ?>
                            <div class="field">
                                <label for="revoke_reason">Revocation reason</label>
                                <input id="revoke_reason" class="input" name="revoke_reason" type="text" required minlength="3" maxlength="255"
                                       placeholder="e.g., Incorrect spelling of student name / withdrawn">
                            </div>
                            <button class="btn btn-danger" type="submit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                                <span>Revoke Certificate</span>
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted mb-0">Only a Super Admin can revoke an issued certificate. Please contact your administrator if changes are required.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">Revoked certificates cannot be edited or re-issued. Create a new corrected certificate if needed.</p>
                <?php endif; ?>

                <?php if($certificate->isDraft() && auth()->user()?->isSuperAdmin()): ?>
                    <div style="height: 1px; background: var(--color-border); margin: var(--space-5) 0;"></div>
                    <form method="post" action="<?php echo e(route('admin.certificates.destroy', $certificate)); ?>"
                          onsubmit="return confirm('Delete this draft permanently?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('delete'); ?>
                        <button class="btn btn-danger btn-sm" type="submit">Delete Draft Permanently</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/certificates/show.blade.php ENDPATH**/ ?>