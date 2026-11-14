<?php $__env->startSection('title', $course->name_en); ?>
<?php $__env->startSection('eyebrow', 'Course Details'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-secondary" href="<?php echo e(route('admin.courses.edit', $course)); ?>">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
        </svg>
        <span>Edit Course</span>
    </a>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.courses.index')); ?>">&larr; Back to list</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Curriculum Specification</h2>
                    <p><?php echo e($course->name_ja); ?></p>
                </div>
                <span class="badge badge-info" style="font-family: var(--font-mono); font-weight: 700; font-size: 0.75rem;"><?php echo e($course->level_code); ?></span>
            </div>
            <div class="card-body">
                <dl class="detail-grid">
                    <dt>English Name</dt>
                    <dd><strong><?php echo e($course->name_en); ?></strong></dd>

                    <dt>Japanese Name</dt>
                    <dd><?php echo e($course->name_ja); ?></dd>

                    <dt>Completion Hours</dt>
                    <dd><span class="badge badge-muted"><?php echo e($course->total_hours); ?> total class hours</span></dd>

                    <dt>Target JLPT Level</dt>
                    <dd><?php echo e($course->aim_level_en); ?> <span class="text-muted">/ <?php echo e($course->aim_level_ja); ?></span></dd>

                    <dt>Core Textbook</dt>
                    <dd><?php echo e($course->book_en); ?> <span class="text-muted">/ <?php echo e($course->book_ja); ?></span></dd>

                    <dt>Default Schedule</dt>
                    <dd><?php echo e($course->default_hours_per_day); ?> hours/day, <?php echo e($course->default_days_per_week); ?> days/week</dd>

                    <dt>Linked Certificates</dt>
                    <dd><strong><?php echo e($course->certificates_count); ?></strong> certificate<?php echo e($course->certificates_count === 1 ? '' : 's'); ?> issued</dd>
                </dl>

                <div class="section-title">Learning Context & Objectives</div>
                <?php if(empty($course->learning_context)): ?>
                    <p class="text-muted mb-0">No context descriptions defined.</p>
                <?php else: ?>
                    <ul class="mb-0" style="padding-left: 20px; line-height: 1.6;">
                        <?php $__currentLoopData = $course->learning_context; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $context): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><strong><?php echo e($context['en'] ?? ''); ?></strong> <span class="text-muted">/ <?php echo e($context['ja'] ?? ''); ?></span></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h2>Usage & Active Credentials</h2>
                        <p><?php echo e($course->certificates_count); ?> certificate<?php echo e($course->certificates_count === 1 ? '' : 's'); ?> enrolled</p>
                    </div>
                    <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.certificates.index', ['q' => $course->name_en])); ?>">Find Certificates</a>
                </div>
                <div class="card-body">
                    <?php if($course->certificates_count > 0): ?>
                        <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: var(--radius-md); padding: var(--space-4); color: #1E40AF; font-size: 0.8125rem;">
                            <strong>In Active Use:</strong> This course is currently attached to <?php echo e($course->certificates_count); ?> student certificate<?php echo e($course->certificates_count === 1 ? '' : 's'); ?>. To maintain cryptographic and historical integrity, it cannot be deleted while certificates reference it.
                        </div>
                    <?php else: ?>
                        <div style="background: #F8FAFC; border: 1px dashed var(--color-border); border-radius: var(--radius-md); padding: var(--space-4); color: #475569; font-size: 0.8125rem;">
                            <strong>Not Yet Enrolled:</strong> No certificates have been issued under this course yet. It can be freely edited or safely removed.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($course->certificates_count === 0): ?>
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h2>Danger Zone</h2>
                            <p>Permanent course removal</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo e(route('admin.courses.destroy', $course)); ?>"
                              onsubmit="return confirm('Permanently delete this course?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('delete'); ?>
                            <button class="btn btn-danger" type="submit">Delete Course</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/courses/show.blade.php ENDPATH**/ ?>