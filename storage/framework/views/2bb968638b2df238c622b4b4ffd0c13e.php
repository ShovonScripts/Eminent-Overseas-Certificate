<?php $__env->startSection('title', 'Verify a certificate — ' . $school['name']); ?>

<?php $__env->startSection('styles'); ?>
    <style>
        .verify-page {
            width: 100%;
            max-width: 640px;
            margin: 32px auto 0;
        }

        .gov-header-actions .btn-outline { display: none; }

        .verify-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
        }

        .verify-heading {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 28px 32px 24px;
            border-bottom: 1px solid #E2E8F0;
        }

        .verify-heading-icon {
            display: grid;
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            place-items: center;
            color: #0A192F;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
        }

        .verify-heading h1 {
            margin: 0;
            color: #0A192F;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.01em;
            line-height: 1.25;
        }

        .verify-heading p {
            margin: 6px 0 0;
            color: #64748B;
            font-size: 14.5px;
            line-height: 1.5;
        }

        .verify-content { padding: 30px 32px 32px; }
        .verify-description { margin: 0 0 24px; color: #475569; font-size: 15px; line-height: 1.65; }
        .verify-label { display: block; margin-bottom: 8px; color: #0A192F; font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        .verify-input-wrap { position: relative; }
        .verify-input-wrap svg { position: absolute; top: 50%; left: 16px; color: #94A3B8; transform: translateY(-50%); pointer-events: none; }
        .verify-input {
            display: block;
            width: 100%;
            min-height: 52px;
            padding: 12px 16px 12px 48px;
            color: #0A192F;
            background: #fff;
            border: 1.5px solid #CBD5E1;
            border-radius: 10px;
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.01em;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }

        .verify-input::placeholder { color: #94A3B8; font-weight: 500; letter-spacing: 0.01em; }
        .verify-input:focus { outline: none; border-color: #0A192F; box-shadow: 0 0 0 4px rgba(10, 25, 47, 0.12); background: #FAFAFA; }
        .verify-input:hover { border-color: #94A3B8; }
        .verify-error { display: flex; align-items: flex-start; gap: 8px; margin-top: 10px; color: #991B1B; font-size: 13px; line-height: 1.5; }
        .verify-error svg { flex: 0 0 16px; margin-top: 1px; }

        .verify-actions { display: flex; gap: 12px; margin-top: 20px; }
        .verify-button, .verify-link {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            border: 1px solid transparent;
            border-radius: 10px;
            font-family: var(--font-sans);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .verify-button { color: #fff; background: #0A192F; border-color: #0A192F; }
        .verify-button:hover { background: #172A45; border-color: #172A45; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(10, 25, 47, 0.2); }
        .verify-button:active { transform: translateY(0); }
        .verify-link { color: #0A192F; background: #fff; border-color: #E2E8F0; }
        .verify-link:hover { background: #F8FAFC; border-color: #CBD5E1; }
        .verify-button:focus-visible, .verify-link:focus-visible { outline: 3px solid rgba(10, 25, 47, 0.3); outline-offset: 2px; }

        .verify-privacy {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 24px;
            padding: 16px 20px;
            color: #475569;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.55;
        }

        .verify-privacy svg { flex: 0 0 18px; margin-top: 1px; color: #B45309; }
        .verify-privacy strong { color: #0A192F; font-weight: 700; }

        /* Input focus-within for label highlight */
        .verify-input-wrap:focus-within svg { color: #0A192F; }

        @media (max-width: 600px) {
            .verify-page { margin-top: 16px; }
            .verify-heading { align-items: flex-start; padding: 24px 20px; }
            .verify-heading-icon { width: 44px; height: 44px; flex-basis: 44px; }
            .verify-heading h1 { font-size: 20px; }
            .verify-content { padding: 24px 20px 28px; }
            .verify-input { min-height: 48px; font-size: 15px; }
            .verify-actions { flex-direction: column; }
            .verify-button, .verify-link { width: 100%; }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="verify-page" aria-labelledby="verify-title">
        <div class="verify-card">
            <div class="verify-heading">
                <div class="verify-heading-icon" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <div>
                    <h1 id="verify-title">Verify a certificate</h1>
                    <p>Check a certificate issued by <?php echo e($school['name']); ?>.</p>
                </div>
            </div>

            <div class="verify-content">
                <p class="verify-description">Enter the registration number printed on the certificate. You can also open this page by scanning its verification QR code.</p>

                <form method="POST" action="<?php echo e(route('verify.lookup')); ?>">
                    <?php echo csrf_field(); ?>
                    <label class="verify-label" for="certificate_no">Certificate registration number</label>
                    <div class="verify-input-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 3h7l5 5v13H5V3h2Z" />
                            <path d="M14 3v6h6M8 13h8M8 17h8" />
                        </svg>
                        <input
                            class="verify-input"
                            id="certificate_no"
                            name="certificate_no"
                            type="text"
                            value="<?php echo e(old('certificate_no')); ?>"
                            placeholder="e.g. <?php echo e($school['number_prefix'] ?? 'EOC'); ?>-2026-000001"
                            autocomplete="off"
                            autofocus
                            aria-describedby="certificate_no_error"
                        >
                    </div>
                    <?php $__errorArgs = ['certificate_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="verify-error" id="certificate_no_error" role="alert">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4m0 4h.01" />
                            </svg>
                            <span><?php echo e($message); ?></span>
                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="verify-actions">
                        <button class="verify-button" type="submit">
                            Verify certificate
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14m-7-7 7 7-7 7" />
                            </svg>
                        </button>
                        <a class="verify-link" href="<?php echo e($school['website'] ?? config('school.website', 'https://eminentoverseas.uk')); ?>" target="_blank" rel="noopener noreferrer">
                            Visit school website
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                <polyline points="15 3 21 3 21 9"/>
                                <line x1="10" y1="14" x2="21" y2="3"/>
                            </svg>
                        </a>
                    </div>
                </form>

                <aside class="verify-privacy">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                    <div><strong>Privacy:</strong> The public record confirms certificate details without showing private contact information or date of birth.</div>
                </aside>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('verify.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/verify/form.blade.php ENDPATH**/ ?>