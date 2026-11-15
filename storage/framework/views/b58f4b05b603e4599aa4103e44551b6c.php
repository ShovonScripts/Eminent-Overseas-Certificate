<?php $__env->startSection('title', 'System Settings'); ?>
<?php $__env->startSection('eyebrow', 'System Configuration'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.dashboard')); ?>">&larr; Back to dashboard</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form method="post" action="<?php echo e(route('admin.settings.update')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('put'); ?>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>School Information</h2>
                    <p>These institutional details appear on certificates, verification pages, and correspondence.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field span-2">
                        <label for="school_name">Official School Name</label>
                        <input id="school_name" class="input" name="school_name" type="text" required
                               value="<?php echo e($settings['school_name'] ?? ''); ?>">
                        <?php $__errorArgs = ['school_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field span-2">
                        <label for="school_address">Campus Address</label>
                        <textarea id="school_address" class="textarea" name="school_address" required rows="3"><?php echo e($settings['school_address'] ?? ''); ?></textarea>
                        <?php $__errorArgs = ['school_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="school_phone">Contact Phone</label>
                        <input id="school_phone" class="input" name="school_phone" type="text" required
                               value="<?php echo e($settings['school_phone'] ?? ''); ?>">
                        <?php $__errorArgs = ['school_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="school_email">Official Email</label>
                        <input id="school_email" class="input" name="school_email" type="email" required
                               value="<?php echo e($settings['school_email'] ?? ''); ?>">
                        <?php $__errorArgs = ['school_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="school_website">Website</label>
                        <input id="school_website" class="input" name="school_website" type="url"
                               value="<?php echo e($settings['school_website'] ?? ''); ?>">
                        <?php $__errorArgs = ['school_website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Certificate Graphical Assets</h2>
                    <p>High-resolution images embedded directly in DomPDF certificate generation.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="logo_path">School Logo</label>
                        <?php if($settings['logo_path'] ?? false): ?>
                            <div class="current-asset">
                                <img src="<?php echo e(asset($settings['logo_path'])); ?>" alt="Current logo" style="max-height: 56px;">
                                <span>Custom logo active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_logo" value="1">
                                <span>Reset to default logo</span>
                            </label>
                        <?php endif; ?>
                        <input id="logo_path" class="input" name="logo_path" type="file" accept="image/*">
                        <div class="help">PNG or JPG, max 2MB. Recommended: 300x300px transparent PNG</div>
                    </div>

                    <div class="field">
                        <label for="background_path">A4 Certificate Border Background</label>
                        <?php if($settings['background_path'] ?? false): ?>
                            <div class="current-asset">
                                <img src="<?php echo e(asset($settings['background_path'])); ?>" alt="Current background" style="max-height: 56px;">
                                <span>Custom border template active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_background" value="1">
                                <span>Reset to default border background</span>
                            </label>
                        <?php endif; ?>
                        <input id="background_path" class="input" name="background_path" type="file" accept="image/*">
                        <div class="help">PNG or JPG, max 2MB. Recommended: A4 2480x3508px portrait</div>
                    </div>

                    <div class="field">
                        <label for="divider_path">Gold Header Divider</label>
                        <?php if($settings['divider_path'] ?? false): ?>
                            <div class="current-asset">
                                <img src="<?php echo e(asset($settings['divider_path'])); ?>" alt="Current divider" style="max-height: 56px;">
                                <span>Custom divider active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_divider" value="1">
                                <span>Reset to default divider</span>
                            </label>
                        <?php endif; ?>
                        <input id="divider_path" class="input" name="divider_path" type="file" accept="image/*">
                        <div class="help">PNG, max 2MB. Recommended: wide tapered gold line (e.g. 1400x20px).</div>
                    </div>

                    <div class="field">
                        <label for="seal_path">Official School Seal / Stamp</label>
                        <?php if($settings['seal_path'] ?? false): ?>
                            <div class="current-asset">
                                <img src="<?php echo e(asset($settings['seal_path'])); ?>" alt="Current seal" style="max-height: 56px;">
                                <span>Custom seal active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_seal" value="1">
                                <span>Reset to default seal</span>
                            </label>
                        <?php endif; ?>
                        <input id="seal_path" class="input" name="seal_path" type="file" accept="image/*">
                        <div class="help">PNG or JPG, max 2MB. Recommended: 300x300px transparent PNG</div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="<?php echo e(route('admin.dashboard')); ?>">Cancel</a>
                <button class="btn btn-primary" type="submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Save Settings</span>
                </button>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/settings/edit.blade.php ENDPATH**/ ?>