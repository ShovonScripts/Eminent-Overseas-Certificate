<?php $__env->startSection('title', 'Verify a Certificate'); ?>

<?php $__env->startSection('content'); ?>
    <div class="banner neutral">
        <div class="icon-wrapper">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>
        <div>
            <h1>Verify a certificate</h1>
            <p>Scan the QR code printed on the certificate, or enter the unique certificate number below.</p>
        </div>
    </div>
    <div class="content">
        <form method="POST" action="<?php echo e(route('verify.lookup')); ?>">
            <?php echo csrf_field(); ?>
            <label for="certificate_no">Certificate Number</label>
            <input id="certificate_no" name="certificate_no" type="text" value="<?php echo e(old('certificate_no')); ?>"
                   placeholder="e.g. EOC-2026-000001" autocomplete="off" autofocus>
            <?php $__errorArgs = ['certificate_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?php echo e($message); ?></span>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <button type="submit" class="btn">
                <span>Verify Credential Authenticity</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <div class="divider"></div>

        <p class="muted" style="margin: 0;">
            <strong>Privacy & Data Protection Notice:</strong> This public portal confirms credential authenticity issued by <?php echo e($school['name']); ?>. To protect student privacy, date of birth, personal address, phone number, and email address are never displayed publicly.
        </p>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('verify.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/verify/form.blade.php ENDPATH**/ ?>