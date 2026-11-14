<?php $__env->startSection('title', $instructor->exists ? 'Edit Instructor' : 'New Instructor'); ?>
<?php $__env->startSection('eyebrow', 'Faculty Management'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.instructors.index')); ?>">&larr; Back to list</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form method="post" action="<?php echo e($instructor->exists ? route('admin.instructors.update', $instructor) : route('admin.instructors.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($instructor->exists): ?>
            <?php echo method_field('put'); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Instructor Profile & Credentials</h2>
                    <p>The instructor's name and title print directly beneath the signature line on official completion certificates.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="name">Full Name</label>
                        <input id="name" class="input" name="name" type="text" required
                               placeholder="e.g. Kenji Tanaka / Dr. Michael Davies"
                               value="<?php echo e(old('name', $instructor->name)); ?>">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="title">Academic / Professional Title</label>
                        <input id="title" class="input" name="title" type="text" required
                               placeholder="e.g. Chief Japanese Language Instructor"
                               value="<?php echo e(old('title', $instructor->title)); ?>">
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="email">Institutional Email <span class="text-muted">(optional)</span></label>
                        <input id="email" class="input" name="email" type="email"
                               placeholder="instructor@eminentoverseas.com"
                               value="<?php echo e(old('email', $instructor->email)); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field flex items-center" style="margin-top: var(--space-4);">
                        <label class="check">
                            <input type="checkbox" name="is_active" value="1"
                                   <?php if(old('is_active', $instructor->is_active ?? true)): echo 'checked'; endif; ?>>
                            <span><strong>Active status</strong> (available for new certificates)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="<?php echo e(route('admin.instructors.index')); ?>">Cancel</a>
                <button class="btn btn-primary" type="submit"><?php echo e($instructor->exists ? 'Save Changes' : 'Create Instructor'); ?></button>
            </div>
        </div>
    </form>

    <?php if($instructor->exists): ?>
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Danger Zone</h2>
                    <p>Instructors tied to existing certificates cannot be deleted to preserve historic certificates.</p>
                </div>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo e(route('admin.instructors.destroy', $instructor)); ?>"
                      onsubmit="return confirm('Permanently delete this instructor profile?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('delete'); ?>
                    <button class="btn btn-danger" type="submit">Delete Instructor</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/instructors/form.blade.php ENDPATH**/ ?>