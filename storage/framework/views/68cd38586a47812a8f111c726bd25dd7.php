<?php $__env->startSection('title', 'Two-factor verification'); ?>

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
                <h1>One more step to confirm it is really you.</h1>
                <p>Open your authenticator application (Google Authenticator, Microsoft Authenticator, 1Password) and enter the 6-digit code.</p>
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
                <div class="eyebrow">Two-Factor Authentication</div>
                <h1>Security Check</h1>
                <p class="lede">Enter the verification code from your authenticator app, or use an emergency recovery code.</p>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
                <?php endif; ?>

                <form method="post" action="<?php echo e(route('two-factor.verify')); ?>" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="field">
                        <label for="code">6-Digit Authenticator Code</label>
                        <input id="code" name="code" type="text" class="input mono" inputmode="numeric"
                               placeholder="123456"
                               style="letter-spacing: 0.3em; font-size: 1.25rem; text-align: center; font-weight: 700;"
                               pattern="[0-9]{6}" maxlength="6" autofocus autocomplete="one-time-code">
                    </div>

                    <div class="field">
                        <label for="recovery_code">Or Emergency Recovery Code</label>
                        <input id="recovery_code" name="recovery_code" type="text" class="input mono"
                               placeholder="xxxx-xxxx-xxxx"
                               autocomplete="off">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-4);">
                        <span>Verify and Continue</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </form>

                <div class="hint">
                    <a href="<?php echo e(route('login')); ?>" style="font-weight: 600;">&larr; Back to sign in</a>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/auth/two-factor-challenge.blade.php ENDPATH**/ ?>