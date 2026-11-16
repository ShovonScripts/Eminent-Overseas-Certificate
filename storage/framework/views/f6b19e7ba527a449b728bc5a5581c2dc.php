<?php $__env->startSection('title', $user->exists ? 'Edit Staff Account' : 'New Staff Account'); ?>
<?php $__env->startSection('eyebrow', 'Staff Administration'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.users.index')); ?>">&larr; Back to accounts</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form method="post" action="<?php echo e($user->exists ? route('admin.users.update', $user) : route('admin.users.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($user->exists): ?>
            <?php echo method_field('put'); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Staff Account Credentials & Role</h2>
                    <p>Password must be at least 8 characters.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="name">Staff Member Full Name</label>
                        <input id="name" class="input" name="name" type="text" required
                               placeholder="e.g. Sarah Jenkins"
                               value="<?php echo e(old('name', $user->name)); ?>">
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
                        <label for="email">Institutional Email Address</label>
                        <input id="email" class="input" name="email" type="email" required
                               placeholder="staff@eminentoverseas.com"
                               value="<?php echo e(old('email', $user->email)); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field span-2">
                        <label for="role">Access Role</label>
                        <select id="role" class="select" name="role" required>
                            <option value="<?php echo e(\App\Models\User::ROLE_STAFF); ?>" <?php if(old('role', $user->role ?? \App\Models\User::ROLE_STAFF) === \App\Models\User::ROLE_STAFF): echo 'selected'; endif; ?>>
                                Staff (Can create drafts, preview and issue certificates)
                            </option>
                            <option value="<?php echo e(\App\Models\User::ROLE_SUPER_ADMIN); ?>" <?php if(old('role', $user->role) === \App\Models\User::ROLE_SUPER_ADMIN): echo 'selected'; endif; ?>>
                                Super Admin (Full access: revoke certificates, audit logs, staff management, settings)
                            </option>
                        </select>
                        <div class="help">Grant Super Admin role only to authorized administrative leads.</div>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="password">Password<?php echo e($user->exists ? ' (leave blank to retain current)' : ''); ?></label>
                        <input id="password" class="input" name="password" type="password" autocomplete="new-password"
                               placeholder="Min. 12 characters"
                               <?php if(! $user->exists): echo 'required'; endif; ?>>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" class="input" name="password_confirmation" type="password"
                               placeholder="Re-enter password"
                               autocomplete="new-password" <?php if(! $user->exists): echo 'required'; endif; ?>>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="<?php echo e(route('admin.users.index')); ?>">Cancel</a>
                <button class="btn btn-primary" type="submit"><?php echo e($user->exists ? 'Save Changes' : 'Create Staff Account'); ?></button>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/users/form.blade.php ENDPATH**/ ?>