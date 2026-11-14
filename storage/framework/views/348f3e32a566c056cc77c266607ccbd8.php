<?php $__env->startSection('title', 'Courses'); ?>
<?php $__env->startSection('eyebrow', 'Reference Data'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-primary" href="<?php echo e(route('admin.courses.create')); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Course</span>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <form class="toolbar" method="get" action="<?php echo e(route('admin.courses.index')); ?>">
            <div style="position: relative;">
                <input class="input" type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search course name or JLPT level..." style="min-width: 300px; padding-left: 36px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <button class="btn btn-secondary" type="submit">Search</button>
            <?php if($search !== ''): ?>
                <a class="btn btn-ghost btn-sm" href="<?php echo e(route('admin.courses.index')); ?>">Clear</a>
            <?php endif; ?>
            <div class="spacer"></div>
            <span class="badge badge-info"><?php echo e($courses->total()); ?> course<?php echo e($courses->total() === 1 ? '' : 's'); ?></span>
        </form>

        <?php if($courses->isEmpty()): ?>
            <div class="empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                </svg>
                <strong>No courses found</strong>
                <p class="text-muted" style="margin-bottom: 16px;">Create a language curriculum course so certificates can reference it.</p>
                <a class="btn btn-primary btn-sm" href="<?php echo e(route('admin.courses.create')); ?>">Create First Course</a>
            </div>
        <?php else: ?>
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>JLPT Level</th>
                        <th class="num">Completion Hours</th>
                        <th class="num">Issued Certificates</th>
                        <th class="actions">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <a class="cell-title" href="<?php echo e(route('admin.courses.show', $course)); ?>"><?php echo e($course->name_en); ?></a>
                                <div class="cell-sub"><?php echo e($course->name_ja); ?></div>
                            </td>
                            <td>
                                <span class="badge badge-info" style="font-family: var(--font-mono); font-weight: 700;"><?php echo e($course->level_code); ?></span>
                            </td>
                            <td class="num"><?php echo e($course->total_hours); ?> hrs</td>
                            <td class="num">
                                <span class="badge badge-muted"><?php echo e($course->certificates_count); ?></span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.courses.show', $course)); ?>">View</a>
                                <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.courses.edit', $course)); ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <?php echo e($courses->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/courses/index.blade.php ENDPATH**/ ?>