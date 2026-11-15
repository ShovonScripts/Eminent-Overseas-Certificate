<?php $__env->startSection('title', 'Staff sign in'); ?>

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
                <h1>Issue, verify and archive completion certificates with confidence.</h1>
                <p>Official bilingual Japanese course certification system. Each credential embeds an immutable cryptographic verification code and QR code for public authenticity validation.</p>
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
                <div class="eyebrow">Staff portal</div>
                <h1>Welcome Back</h1>
                <p class="lede">Sign in to your administrative account to manage certificates.</p>

                <?php if(session('status')): ?>
                    <div class="alert alert-success"><?php echo e(session('status')); ?></div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo e(route('login.store')); ?>" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" class="input" value="<?php echo e(old('email')); ?>"
                               placeholder="admin@eminentoverseas.com"
                               required autofocus autocomplete="username">
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" class="input"
                               placeholder="••••••••••••"
                               required autocomplete="current-password">
                    </div>

                    <div class="field flex items-center justify-between" style="margin-bottom: var(--space-6);">
                        <label class="check" style="margin-bottom: 0;">
                            <input type="checkbox" name="remember" value="1" <?php if(old('remember')): echo 'checked'; endif; ?>>
                            <span>Keep me signed in</span>
                        </label>
                        <a href="<?php echo e(route('password.request')); ?>" style="font-size: var(--text-xs); font-weight: 600;">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <span>Sign In</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </form>

                <div class="hint">
                    No account yet? Run <code class="mono">php artisan admin:create</code> on the server to create the initial administrator.
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/auth/login.blade.php ENDPATH**/ ?>