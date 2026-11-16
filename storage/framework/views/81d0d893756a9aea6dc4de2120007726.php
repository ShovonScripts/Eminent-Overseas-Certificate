<?php $__env->startSection('title', 'Choose a new password'); ?>

<?php $__env->startSection('body'); ?>
    <div class="auth-page">
        <section class="auth-art">
            <div class="brand-lockup">
                <img src="<?php echo e(asset($schoolConfig['images']['logo'])); ?>" alt="<?php echo e($schoolConfig['name']); ?>">
                <div>
                    <div class="name"><?php echo e($schoolConfig['name']); ?></div>
                    <div class="sub">Certificate Administration Portal</div>
                </div>
            </div>

            <div class="pitch">
                <h1>Choose a strong, unique password.</h1>
                <p>Password must be at least 8 characters.</p>
            </div>

            <div class="meta">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Authorized Staff Access Only &middot; <?php echo e($schoolConfig['address']); ?></span>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="eyebrow">Staff Portal</div>
                <h1>New Password</h1>
                <p class="lede">Set a secure password for your administrative staff account.</p>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
                <?php endif; ?>

                <form method="post" action="<?php echo e(route('password.update')); ?>" novalidate>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="token" value="<?php echo e($token); ?>">

                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" class="input" value="<?php echo e(old('email', $email)); ?>"
                               required autocomplete="username">
                    </div>

                    <div class="field">
                        <label for="password">New password</label>
                        <input id="password" name="password" type="password" class="input"
                               placeholder="Min. 12 characters"
                               required autofocus autocomplete="new-password">
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input"
                               placeholder="Re-enter password"
                               required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-4);">
                        <span>Save New Password</span>
                    </button>
                </form>

                <div class="hint">
                    <a href="<?php echo e(route('login')); ?>" style="font-weight: 600;">Back to sign in</a>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/auth/reset-password.blade.php ENDPATH**/ ?>