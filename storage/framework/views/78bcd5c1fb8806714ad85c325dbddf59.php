<?php $__env->startSection('title', 'Certificates'); ?>
<?php $__env->startSection('eyebrow', 'Issuing & Management'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-secondary" href="<?php echo e(route('admin.certificates.import.form')); ?>">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        <span>Import CSV</span>
    </a>
    <a class="btn btn-primary" href="<?php echo e(route('admin.certificates.create')); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Certificate</span>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="toolbar">
            <div class="flex items-center gap-2" style="flex-wrap: wrap;">
                <a class="btn <?php echo e($status === null ? 'btn-primary' : 'btn-secondary'); ?> btn-sm" href="<?php echo e(route('admin.certificates.index', array_filter(['q' => $search ?: null]))); ?>">
                    All <span style="opacity: 0.8; font-weight: 500;">(<?php echo e($counts['all']); ?>)</span>
                </a>
                <a class="btn <?php echo e($status === \App\Enums\CertificateStatus::Draft ? 'btn-primary' : 'btn-secondary'); ?> btn-sm" href="<?php echo e(route('admin.certificates.index', array_filter(['status' => 'draft', 'q' => $search ?: null]))); ?>">
                    Drafts <span style="opacity: 0.8; font-weight: 500;">(<?php echo e($counts['draft']); ?>)</span>
                </a>
                <a class="btn <?php echo e($status === \App\Enums\CertificateStatus::Issued ? 'btn-primary' : 'btn-secondary'); ?> btn-sm" href="<?php echo e(route('admin.certificates.index', array_filter(['status' => 'issued', 'q' => $search ?: null]))); ?>">
                    Issued <span style="opacity: 0.8; font-weight: 500;">(<?php echo e($counts['issued']); ?>)</span>
                </a>
                <a class="btn <?php echo e($status === \App\Enums\CertificateStatus::Revoked ? 'btn-primary' : 'btn-secondary'); ?> btn-sm" href="<?php echo e(route('admin.certificates.index', array_filter(['status' => 'revoked', 'q' => $search ?: null]))); ?>">
                    Revoked <span style="opacity: 0.8; font-weight: 500;">(<?php echo e($counts['revoked']); ?>)</span>
                </a>
            </div>

            <div class="spacer"></div>

            <form class="flex items-center gap-2" method="get" action="<?php echo e(route('admin.certificates.index')); ?>">
                <?php if($status): ?>
                    <input type="hidden" name="status" value="<?php echo e($status->value); ?>">
                <?php endif; ?>
                <div style="position: relative;">
                    <input class="input" type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search certificate #, ULID or student..." style="min-width: 280px; padding-left: 36px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <button class="btn btn-secondary" type="submit">Search</button>
                <?php if($search !== ''): ?>
                    <a class="btn btn-ghost btn-sm" href="<?php echo e(route('admin.certificates.index', $status ? ['status' => $status->value] : [])); ?>">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if($certificates->isEmpty()): ?>
            <div class="empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <circle cx="12" cy="8" r="6"/>
                    <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/>
                </svg>
                <strong>No certificates match your query</strong>
                <?php if($search !== '' || $status): ?>
                    <p class="text-muted" style="margin-bottom: 16px;">Try adjusting your filter or search keywords.</p>
                    <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.certificates.index')); ?>">Reset filters</a>
                <?php else: ?>
                    <p class="text-muted" style="margin-bottom: 16px;">Get started by issuing the first student certificate.</p>
                    <a class="btn btn-primary" href="<?php echo e(route('admin.certificates.create')); ?>">Create First Certificate</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Certificate</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Study Period</th>
                        <th>Issued On</th>
                        <th>Status</th>
                        <th class="actions">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <a class="cell-title" href="<?php echo e(route('admin.certificates.show', $certificate)); ?>">
                                    <?php echo e($certificate->certificate_no ?? 'Draft #'.$certificate->id); ?>

                                </a>
                                <?php if($certificate->verification_code): ?>
                                    <div class="cell-sub mono" style="font-size: 0.7rem; color: #64748B;"><?php echo e($certificate->verification_code); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: #0F172A;"><?php echo e($certificate->student_name_en); ?></strong>
                                <?php if($certificate->student_name_ja): ?>
                                    <div class="cell-sub"><?php echo e($certificate->student_name_ja); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?php echo e($certificate->course?->name_en); ?></div>
                                <span class="badge badge-muted" style="font-size: 0.65rem; padding: 1px 6px; margin-top: 2px;"><?php echo e($certificate->course?->level_code); ?></span>
                            </td>
                            <td class="text-muted" style="font-size: var(--text-xs); white-space: nowrap;">
                                <?php echo e($certificate->study_start?->format('Y-m-d')); ?> &rarr; <?php echo e($certificate->study_end?->format('Y-m-d')); ?>

                            </td>
                            <td class="text-muted" style="font-size: var(--text-xs);">
                                <?php echo e($certificate->issued_on?->format('Y-m-d') ?? '—'); ?>

                            </td>
                            <td>
                                <span class="badge badge-<?php echo e($certificate->status->value); ?>"><?php echo e($certificate->status->label()); ?></span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.certificates.show', $certificate)); ?>">View</a>
                                <?php if($certificate->isDraft()): ?>
                                    <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.certificates.edit', $certificate)); ?>">Edit</a>
                                <?php endif; ?>
                                <?php if(($certificate->isDraft() || $certificate->isRevoked()) && auth()->user()?->isSuperAdmin()): ?>
                                    <form method="post" action="<?php echo e(route('admin.certificates.destroy', $certificate)); ?>"
                                          style="display: inline-flex; margin: 0;"
                                          onsubmit="return confirm('Delete this certificate permanently?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('delete'); ?>
                                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <?php echo e($certificates->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/certificates/index.blade.php ENDPATH**/ ?>