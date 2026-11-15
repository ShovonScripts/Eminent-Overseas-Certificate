<?php $__env->startSection('title', 'Staff accounts'); ?>
<?php $__env->startSection('eyebrow', 'System Administration'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-primary" href="<?php echo e(route('admin.users.create')); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Staff Account</span>
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-head">
            <div>
                <h2>Authorized Staff Accounts</h2>
                <p>Staff members can create and issue student certificates. Super Admins can revoke credentials and administer staff logins.</p>
            </div>
            <span class="badge badge-info"><?php echo e($users->total()); ?> account<?php echo e($users->total() === 1 ? '' : 's'); ?></span>
        </div>

        <div class="table-wrap" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email Address</th>
                    <th>System Role</th>
                    <th>Member Since</th>
                    <th class="actions">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <strong style="color: #0F172A;"><?php echo e($user->name); ?></strong>
                            <?php if($user->id === auth()->id()): ?>
                                <span class="badge badge-gold" style="font-size: 0.65rem; margin-left: 6px;">Current User</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?php echo e($user->email); ?></td>
                        <td>
                            <span class="badge <?php echo e($user->isSuperAdmin() ? 'badge-issued' : 'badge-muted'); ?>">
                                <?php echo e($user->isSuperAdmin() ? 'Super Admin' : 'Staff'); ?>

                            </span>
                        </td>
                        <td class="text-muted"><?php echo e($user->created_at?->format('Y-m-d')); ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?php echo e(route('admin.users.edit', $user)); ?>">Edit</a>
                            <?php if($user->id !== auth()->id()): ?>
                                <form method="post" action="<?php echo e(route('admin.users.destroy', $user)); ?>"
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Remove this staff account?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('delete'); ?>
                                    <button class="btn btn-danger btn-sm" type="submit">Remove</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            <?php echo e($users->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/users/index.blade.php ENDPATH**/ ?>