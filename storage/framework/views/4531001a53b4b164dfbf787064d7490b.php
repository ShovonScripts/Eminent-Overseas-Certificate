<?php ($contexts = old('learning_context', $course->learning_context ?: [['ja' => '', 'en' => '']])); ?>
<?php ($contexts = array_values($contexts) ?: [['ja' => '', 'en' => '']]); ?>

<?php $__env->startSection('title', $course->exists ? 'Edit Course' : 'New Course'); ?>
<?php $__env->startSection('eyebrow', 'Curriculum Management'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <?php if($course->exists): ?>
        <a class="btn btn-secondary" href="<?php echo e(route('admin.courses.show', $course)); ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
            <span>View Course</span>
        </a>
    <?php endif; ?>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.courses.index')); ?>">&larr; Back to list</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form method="post" action="<?php echo e($course->exists ? route('admin.courses.update', $course) : route('admin.courses.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($course->exists): ?>
            <?php echo method_field('put'); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Course Definition & Nomenclature</h2>
                    <p>Course specifications are permanently embedded into certificates upon issue and cannot be modified retroactively.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="name_en">English Course Name</label>
                        <input id="name_en" class="input" name="name_en" type="text" required
                               placeholder="e.g. Japanese Basic Course"
                               value="<?php echo e(old('name_en', $course->name_en)); ?>">
                        <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="name_ja">Japanese Course Name</label>
                        <input id="name_ja" class="input" name="name_ja" type="text" required
                               placeholder="e.g. 日本語基礎コース"
                               value="<?php echo e(old('name_ja', $course->name_ja)); ?>">
                        <?php $__errorArgs = ['name_ja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="level_code">JLPT Level Code</label>
                        <input id="level_code" class="input mono" name="level_code" type="text" required
                               value="<?php echo e(old('level_code', $course->level_code)); ?>" placeholder="e.g. N5, N4, N3">
                        <?php $__errorArgs = ['level_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="total_hours">Total Completion Hours</label>
                        <input id="total_hours" class="input" name="total_hours" type="number" min="1" max="3000" required
                               placeholder="150"
                               value="<?php echo e(old('total_hours', $course->total_hours)); ?>">
                        <?php $__errorArgs = ['total_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="aim_level_en">Aim Level (English)</label>
                        <input id="aim_level_en" class="input" name="aim_level_en" type="text" required
                               placeholder="JLPT N5 level"
                               value="<?php echo e(old('aim_level_en', $course->aim_level_en)); ?>">
                        <?php $__errorArgs = ['aim_level_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="aim_level_ja">Aim Level (Japanese)</label>
                        <input id="aim_level_ja" class="input" name="aim_level_ja" type="text" required
                               placeholder="日本語能力試験 N5 レベル"
                               value="<?php echo e(old('aim_level_ja', $course->aim_level_ja)); ?>">
                        <?php $__errorArgs = ['aim_level_ja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="book_en">Textbook (English)</label>
                        <input id="book_en" class="input" name="book_en" type="text" required
                               placeholder="Minna no Nihongo I"
                               value="<?php echo e(old('book_en', $course->book_en)); ?>">
                        <?php $__errorArgs = ['book_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="book_ja">Textbook (Japanese)</label>
                        <input id="book_ja" class="input" name="book_ja" type="text" required
                               placeholder="みんなの日本語 初級 I"
                               value="<?php echo e(old('book_ja', $course->book_ja)); ?>">
                        <?php $__errorArgs = ['book_ja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="default_hours_per_day">Default Hours Per Day</label>
                        <input id="default_hours_per_day" class="input" name="default_hours_per_day" type="number" min="1" max="12" required
                               value="<?php echo e(old('default_hours_per_day', $course->default_hours_per_day ?? 3)); ?>">
                    </div>
                    <div class="field">
                        <label for="default_days_per_week">Default Days Per Week</label>
                        <input id="default_days_per_week" class="input" name="default_days_per_week" type="number" min="1" max="7" required
                               value="<?php echo e(old('default_days_per_week', $course->default_days_per_week ?? 3)); ?>">
                    </div>
                </div>

                <div class="section-title">Bilingual Learning Context</div>
                <p class="text-muted" style="margin-bottom: var(--space-4); font-size: 0.8125rem;">Bullet points rendered on certificates describing learning achievements and topics.</p>

                <div id="context-rows" style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <?php $__currentLoopData = $contexts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $context): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-grid context-row" style="background: #F8FAFC; padding: var(--space-4); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                            <div class="field mb-0">
                                <label>Japanese Description</label>
                                <input class="input" name="learning_context[<?php echo e($index); ?>][ja]" type="text"
                                       placeholder="ひらがな、カタカナ、基本的な漢字の読み書き"
                                       value="<?php echo e($context['ja'] ?? ''); ?>">
                                <?php $__errorArgs = ["learning_context.{$index}.ja"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="field mb-0">
                                <label>English Description</label>
                                <div class="flex gap-2">
                                    <input class="input" name="learning_context[<?php echo e($index); ?>][en]" type="text"
                                           placeholder="Reading and writing Hiragana, Katakana and basic Kanji"
                                           value="<?php echo e($context['en'] ?? ''); ?>">
                                    <button class="btn btn-danger btn-sm" type="button" data-remove-context>Remove</button>
                                </div>
                                <?php $__errorArgs = ["learning_context.{$index}.en"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div style="margin-top: var(--space-4);">
                    <button class="btn btn-secondary btn-sm" type="button" id="add-context">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>Add Context Line</span>
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="<?php echo e(route('admin.courses.index')); ?>">Cancel</a>
                <button class="btn btn-primary" type="submit"><?php echo e($course->exists ? 'Save Changes' : 'Create Course'); ?></button>
            </div>
        </div>
    </form>

    <template id="context-template">
        <div class="form-grid context-row" style="background: #F8FAFC; padding: var(--space-4); border-radius: var(--radius-md); border: 1px solid var(--color-border);">
            <div class="field mb-0">
                <label>Japanese Description</label>
                <input class="input" name="learning_context[__INDEX__][ja]" type="text">
            </div>
            <div class="field mb-0">
                <label>English Description</label>
                <div class="flex gap-2">
                    <input class="input" name="learning_context[__INDEX__][en]" type="text">
                    <button class="btn btn-danger btn-sm" type="button" data-remove-context>Remove</button>
                </div>
            </div>
        </div>
    </template>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (() => {
            const rows = document.getElementById('context-rows');
            const template = document.getElementById('context-template');
            let next = <?php echo e(count($contexts)); ?>;

            document.getElementById('add-context').addEventListener('click', () => {
                const html = template.innerHTML.replaceAll('__INDEX__', String(next++));
                rows.insertAdjacentHTML('beforeend', html);
            });

            rows.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-context]');
                if (!button) return;
                const row = button.closest('.context-row');
                if (rows.querySelectorAll('.context-row').length > 1) {
                    row.remove();
                }
            });
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/courses/form.blade.php ENDPATH**/ ?>