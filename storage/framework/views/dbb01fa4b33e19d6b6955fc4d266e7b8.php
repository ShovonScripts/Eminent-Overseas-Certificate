<?php $__env->startSection('title', 'Instructors'); ?>
<?php $__env->startSection('eyebrow', 'Reference Data'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-primary" href="<?php echo e(route('admin.instructors.create')); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Instructor</span>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <form class="toolbar" method="get" action="<?php echo e(route('admin.instructors.index')); ?>">
            <div style="position: relative;">
                <input class="input" type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search instructor name or email..." style="min-width: 300px; padding-left: 36px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <button class="btn btn-secondary" type="submit">Search</button>
            <?php if($search !== ''): ?>
                <a class="btn btn-ghost btn-sm" href="<?php echo e(route('admin.instructors.index')); ?>">Clear</a>
            <?php endif; ?>
            <div class="spacer"></div>
            <span class="badge badge-info"><?php echo e($instructors->total()); ?> instructor<?php echo e($instructors->total() === 1 ? '' : 's'); ?></span>
        </form>

        <?php if($instructors->isEmpty()): ?>
            <div class="empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
                <strong>No instructors found</strong>
                <p class="text-muted" style="margin-bottom: 16px;">Add teaching instructors so their signatures appear on issued certificates.</p>
                <a class="btn btn-primary btn-sm" href="<?php echo e(route('admin.instructors.create')); ?>">Add First Instructor</a>
            </div>
        <?php else: ?>
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Instructor</th>
                        <th>Professional Title</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="num">Signed Certificates</th>
                        <th class="actions">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong style="color: #0F172A;"><?php echo e($instructor->name); ?></strong>
                            </td>
                            <td><?php echo e($instructor->title); ?></td>
                            <td class="text-muted"><?php echo e($instructor->email ?: '—'); ?></td>
                            <td>
                                <span class="badge <?php echo e($instructor->is_active ? 'badge-issued' : 'badge-draft'); ?>">
                                    <?php echo e($instructor->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </td>
                            <td class="num">
                                <span class="badge badge-muted"><?php echo e($instructor->certificates_count); ?></span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.instructors.edit', $instructor)); ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <?php echo e($instructors->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/instructors/index.blade.php ENDPATH**/ ?>