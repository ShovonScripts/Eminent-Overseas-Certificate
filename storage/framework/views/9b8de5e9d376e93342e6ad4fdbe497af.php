<?php $__env->startSection('title', 'Certificate Revoked'); ?>

<?php $__env->startSection('content'); ?>
    <div class="banner revoked">
        <div class="icon-wrapper">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
        </div>
        <div>
            <h1>Certificate Has Been Revoked</h1>
            <p>This certificate is no longer valid. Contact <?php echo e($school['name']); ?> for verification assistance.</p>
        </div>
    </div>
    <div class="content">
        <dl>
            <dt>Certificate No.</dt>
            <dd><span class="mono-val"
                    style="color: #991B1B; background: #FEF2F2; border-color: #FECACA;"><?php echo e($certificate->certificate_no); ?></span>
            </dd>

            <dt>Current Status</dt>
            <dd><span
                    style="color: #991B1B; background: #FEF2F2; padding: 2px 10px; border-radius: 9999px; font-size: 13px; font-weight: 700; border: 1px solid #FECACA;">&bull;
                    Revoked & Invalid</span></dd>

            <?php if($certificate->revoked_at): ?>
                <dt>Revoked On</dt>
                <dd><?php echo e($certificate->revoked_at->format('Y-m-d H:i')); ?></dd>
            <?php endif; ?>
        </dl>

        <p class="muted" style="margin-top: 24px;">
            For student privacy and security compliance, personal details and academic summaries are not displayed for
            revoked credentials.
        </p>

        <a class="btn" href="<?php echo e(route('verify.form')); ?>">
            <span>Verify Another Certificate</span>
        </a>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('verify.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/verify/revoked.blade.php ENDPATH**/ ?>