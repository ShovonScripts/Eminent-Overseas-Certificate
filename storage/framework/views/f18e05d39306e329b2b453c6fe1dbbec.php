<?php
    $isEdit = $certificate->exists;
    $selectedDays = old('class_days', $certificate->class_days ?? []);
    $selectedDays = is_array($selectedDays) ? $selectedDays : [];
    if (!$isEdit && $selectedDays === []) {
        $selectedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    }
    $selectedInstructors = array_values((array) old('instructors', $certificate->instructors->pluck('id')->all()));
    $selectedInstructors = array_pad($selectedInstructors, 2, '');
    $attendanceRows = old('attendances', $certificate->attendances->map(fn ($a) => [
        'year' => $a->year,
        'month' => $a->month,
        'class_hours' => $a->class_hours,
        'attendance_hours' => $a->attendance_hours,
    ])->all());
    $attendanceRows = is_array($attendanceRows) ? array_values($attendanceRows) : [];
    if ($attendanceRows === []) {
        $attendanceRows = [['year' => now()->year, 'month' => now()->month, 'class_hours' => 0, 'attendance_hours' => 0]];
    }
?>

<?php $__env->startSection('title', $isEdit ? 'Edit draft certificate' : 'New certificate'); ?>
<?php $__env->startSection('eyebrow', 'Certificates'); ?>

<?php $__env->startSection('topbar_actions'); ?>
    <?php if($isEdit): ?>
        <a class="btn btn-secondary" href="<?php echo e(route('certificates.preview', $certificate)); ?>" target="_blank" rel="noopener">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            <span>Preview PDF</span>
        </a>
    <?php endif; ?>
    <a class="btn btn-ghost" href="<?php echo e(route('admin.certificates.index')); ?>">&larr; Back to list</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form id="certificate-form" method="post"
          action="<?php echo e($isEdit ? route('admin.certificates.update', $certificate) : route('admin.certificates.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($isEdit): ?>
            <?php echo method_field('put'); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">01</span>
                    <div>
                        <h2>Student Personal Information</h2>
                        <p>Enter student details. A master student record will be linked or generated automatically.</p>
                    </div>
                </div>
                <?php if($isEdit): ?>
                    <span class="badge badge-draft">Draft #<?php echo e($certificate->id); ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="student_name_en">English name</label>
                        <input id="student_name_en" class="input" name="student_name_en" type="text" required
                               value="<?php echo e(old('student_name_en', $certificate->student_name_en ?? '')); ?>">
                        <?php $__errorArgs = ['student_name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="student_name_ja">Japanese name <span class="text-muted">(optional)</span></label>
                        <input id="student_name_ja" class="input" name="student_name_ja" type="text"
                               value="<?php echo e(old('student_name_ja', $certificate->student_name_ja ?? '')); ?>">
                        <?php $__errorArgs = ['student_name_ja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="student_date_of_birth">Date of birth</label>
                        <input id="student_date_of_birth" class="input" name="student_date_of_birth" type="date" required
                               value="<?php echo e(old('student_date_of_birth', $certificate->student_date_of_birth?->format('Y-m-d') ?? '')); ?>">
                        <?php $__errorArgs = ['student_date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="student_gender">Sex</label>
                        <select id="student_gender" class="select" name="student_gender" required>
                            <option value="">Select…</option>
                            <?php $__currentLoopData = \App\Enums\Gender::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($gender->value); ?>" <?php if(old('student_gender', $certificate->student_gender?->value) === $gender->value): echo 'selected'; endif; ?>>
                                    <?php echo e($gender->label()); ?> / <?php echo e($gender->japaneseLabel()); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['student_gender'];
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
                <div class="card-head-title">
                    <span class="step-badge">02</span>
                    <div>
                        <h2>Course Curriculum</h2>
                        <p>Select the recognized Japanese curriculum for this completion certificate.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="course_id">Course</label>
                        <select id="course_id" class="select" name="course_id" required>
                            <option value="">Select a course…</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>"
                                        data-hours="<?php echo e($course->default_hours_per_day); ?>"
                                        data-days="<?php echo e($course->default_days_per_week); ?>"
                                        <?php if((string) old('course_id', $certificate->course_id) === (string) $course->id): echo 'selected'; endif; ?>>
                                    <?php echo e($course->name_en); ?> (<?php echo e($course->level_code); ?>) — <?php echo e($course->total_hours); ?>h
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['course_id'];
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
                <div class="card-head-title">
                    <span class="step-badge">03</span>
                    <div>
                        <h2>Study Period & Schedule</h2>
                        <p>Class dates, duration, and scheduled days as they will appear on the physical certificate.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="study_start">Study start</label>
                        <input id="study_start" class="input" name="study_start" type="date" required
                               value="<?php echo e(old('study_start', $certificate->study_start?->format('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['study_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="study_end">Study end</label>
                        <input id="study_end" class="input" name="study_end" type="date" required
                               value="<?php echo e(old('study_end', $certificate->study_end?->format('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['study_end'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="hours_per_day">Hours per day</label>
                        <input id="hours_per_day" class="input" name="hours_per_day" type="number" min="1" max="12" required
                               value="<?php echo e(old('hours_per_day', $certificate->hours_per_day ?? 3)); ?>">
                        <?php $__errorArgs = ['hours_per_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label for="days_per_week">Days per week</label>
                        <input id="days_per_week" class="input" name="days_per_week" type="number" min="1" max="7" required
                               value="<?php echo e(old('days_per_week', $certificate->days_per_week ?? 5)); ?>">
                        <?php $__errorArgs = ['days_per_week'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field span-2">
                        <div class="schedule-presets">
                            <span class="preset-title">Presets:</span>
                            <button type="button" class="preset-btn" data-days="Monday,Wednesday,Friday" data-hours="3">Mon, Wed, Fri (3d×3h)</button>
                            <button type="button" class="preset-btn" data-days="Monday,Tuesday,Wednesday,Thursday,Friday" data-hours="3">Mon–Fri (5d×3h)</button>
                            <button type="button" class="preset-btn" data-days="Saturday,Sunday" data-hours="4">Sat &amp; Sun (2d×4h)</button>
                            <button type="button" class="preset-btn" data-days="Saturday,Monday,Wednesday" data-hours="3">Sat, Mon, Wed (3d×3h)</button>
                        </div>
                        <span class="label">Class days</span>
                        <div class="check-grid">
                            <?php $__currentLoopData = \App\Http\Requests\CertificateRequest::WEEKDAYS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="check">
                                    <input type="checkbox" name="class_days[]" value="<?php echo e($day); ?>"
                                           <?php if(in_array($day, $selectedDays, true)): echo 'checked'; endif; ?>>
                                    <?php echo e($day); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['class_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field span-2">
                        <label for="national_holidays">Other holidays <span class="text-muted">(optional)</span></label>
                        <textarea id="national_holidays" class="textarea" name="national_holidays"
                                  placeholder="6月9日; 6月11日; 8月16日; 10月1日&#10;or one Y-m-d date per line"><?php echo e(old('national_holidays', $certificate->national_holidays)); ?></textarea>
                        <div class="help">Separate dates with semicolons, commas or new lines. Both Japanese dates and Y-m-d work.</div>
                        <div class="holiday-tags">
                            <span class="holiday-chip" data-holiday="1月1日 (元日)">+ 1月1日 (元日)</span>
                            <span class="holiday-chip" data-holiday="2月23日 (天皇誕生日)">+ 2月23日 (天皇誕生日)</span>
                            <span class="holiday-chip" data-holiday="5月3日 (憲法記念日)">+ 5月3日 (憲法記念日)</span>
                            <span class="holiday-chip" data-holiday="5月5日 (こどもの日)">+ 5月5日 (こどもの日)</span>
                            <span class="holiday-chip" data-holiday="8月15日 (お盆)">+ 8月15日 (お盆)</span>
                            <span class="holiday-chip" data-holiday="9月23日 (秋分の日)">+ 9月23日 (秋分の日)</span>
                            <span class="holiday-chip" data-holiday="11月23日 (勤労感謝の日)">+ 11月23日 (勤労感謝の日)</span>
                        </div>
                        <?php $__errorArgs = ['national_holidays'];
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
                <div class="card-head-title">
                    <span class="step-badge">04</span>
                    <div>
                        <h2>Monthly Attendance Breakdown</h2>
                        <p>Add or adjust months freely. Course completion hours on the PDF are calculated from these rows.</p>
                    </div>
                </div>
                <div class="card-head-actions">
                    <button class="btn btn-secondary btn-sm" type="button" id="match-attendance" title="Fill attendance hours equal to class hours (100% attendance)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Match 100%</span>
                    </button>
                    <button class="btn btn-gold btn-sm" type="button" id="suggest-hours">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        <span id="suggest-hours-label">Suggest class hours</span>
                    </button>
                </div>
            </div>

            <div id="suggest-error" class="alert alert-danger" style="display: none; margin: 16px 20px 0;"></div>

            <div class="table-wrap">
                <table class="table attendance-table">
                    <thead>
                    <tr>
                        <th>Year</th>
                        <th>Month</th>
                        <th class="num">Class hours</th>
                        <th class="num">Attendance hours</th>
                        <th class="num">Rate</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="attendance-rows">
                    <?php $__currentLoopData = $attendanceRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="attendance-row">
                            <td>
                                <input class="input" type="number" name="attendances[<?php echo e($index); ?>][year]" min="2000" max="2100" required
                                       value="<?php echo e($row['year'] ?? now()->year); ?>">
                            </td>
                            <td>
                                <input class="input" type="number" name="attendances[<?php echo e($index); ?>][month]" min="1" max="12" required
                                       value="<?php echo e($row['month'] ?? 1); ?>">
                            </td>
                            <td class="num">
                                <input class="input" type="number" name="attendances[<?php echo e($index); ?>][class_hours]" min="0" max="2000" required
                                       value="<?php echo e($row['class_hours'] ?? 0); ?>">
                            </td>
                            <td class="num">
                                <input class="input" type="number" name="attendances[<?php echo e($index); ?>][attendance_hours]" min="0" max="2000" required
                                       value="<?php echo e($row['attendance_hours'] ?? 0); ?>">
                            </td>
                            <td class="rate" data-rate>0%</td>
                            <td class="actions">
                                <button class="btn btn-danger btn-sm" type="button" data-remove-row>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                    <span>Remove</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="attendance-totals" aria-live="polite">
                <div class="item">
                    <span class="label">Total class hours</span>
                    <strong data-total-class>0</strong>
                </div>
                <div class="item">
                    <span class="label">Total attendance hours</span>
                    <strong data-total-attendance>0</strong>
                </div>
                <div class="item">
                    <span class="label">Overall attendance rate</span>
                    <strong data-total-rate>0%</strong>
                </div>
                <div class="item" style="margin-left: auto;">
                    <span class="label">ISA Guideline</span>
                    <div id="immigration-badge" class="immigration-threshold-badge unmet">
                        <span id="immigration-status">⚠️ Calculating…</span>
                    </div>
                </div>
            </div>

            <div class="card-body" style="padding-top: var(--space-4);">
                <button class="btn btn-secondary btn-sm" type="button" id="add-attendance-row">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add month</span>
                </button>
                <?php $__errorArgs = ['attendances'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">05</span>
                    <div>
                        <h2>Signatures & Issue Date</h2>
                        <p>Assign authorizing instructors. The primary instructor prints on the left signature line.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="instructor_1">Left instructor</label>
                        <select id="instructor_1" class="select" name="instructors[]" required>
                            <option value="">Select an instructor…</option>
                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($instructor->id); ?>" <?php if((string) $selectedInstructors[0] === (string) $instructor->id): echo 'selected'; endif; ?>>
                                    <?php echo e($instructor->name); ?> — <?php echo e($instructor->title); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="instructor_2">Right instructor <span class="text-muted">(optional)</span></label>
                        <select id="instructor_2" class="select" name="instructors[]">
                            <option value="">No second instructor</option>
                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($instructor->id); ?>" <?php if((string) $selectedInstructors[1] === (string) $instructor->id): echo 'selected'; endif; ?>>
                                    <?php echo e($instructor->name); ?> — <?php echo e($instructor->title); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="issued_on">Date of issue</label>
                        <input id="issued_on" class="input" name="issued_on" type="date" required
                               value="<?php echo e(old('issued_on', $certificate->issued_on?->format('Y-m-d'))); ?>">
                        <div class="help">Must be on or after the study end date.</div>
                        <?php $__errorArgs = ['issued_on'];
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

            <div class="form-actions">
                <a class="btn btn-secondary" href="<?php echo e($isEdit ? route('admin.certificates.show', $certificate) : route('admin.certificates.index')); ?>">Cancel</a>
                <button class="btn btn-primary" type="submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span><?php echo e($isEdit ? 'Save draft' : 'Create draft'); ?></span>
                </button>
            </div>
        </div>

    </form>

    <template id="attendance-template">
        <tr class="attendance-row">
            <td><input class="input" type="number" name="attendances[__INDEX__][year]" min="2000" max="2100" required value="<?php echo e(now()->year); ?>"></td>
            <td><input class="input" type="number" name="attendances[__INDEX__][month]" min="1" max="12" required value="1"></td>
            <td class="num"><input class="input" type="number" name="attendances[__INDEX__][class_hours]" min="0" max="2000" required value="0"></td>
            <td class="num"><input class="input" type="number" name="attendances[__INDEX__][attendance_hours]" min="0" max="2000" required value="0"></td>
            <td class="rate" data-rate>0%</td>
            <td class="actions">
                <button class="btn btn-danger btn-sm" type="button" data-remove-row>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    <span>Remove</span>
                </button>
            </td>
        </tr>
    </template>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (() => {
            const form = document.getElementById('certificate-form');
            const rows = document.getElementById('attendance-rows');
            const template = document.getElementById('attendance-template');
            const suggestButton = document.getElementById('suggest-hours');
            const suggestLabel = document.getElementById('suggest-hours-label') || suggestButton;
            const suggestError = document.getElementById('suggest-error');
            const matchButton = document.getElementById('match-attendance');
            let nextIndex = <?php echo e(count($attendanceRows)); ?>;

            const refreshTotals = () => {
                let totalClass = 0;
                let totalAttendance = 0;

                rows.querySelectorAll('.attendance-row').forEach((row) => {
                    const classInput = row.querySelector('[name$="[class_hours]"]');
                    const attendInput = row.querySelector('[name$="[attendance_hours]"]');
                    const classHours = Number(classInput?.value || 0);
                    const attendance = Number(attendInput?.value || 0);

                    // Validation: attendance cannot exceed class hours
                    if (attendance > classHours && classHours > 0) {
                        attendInput?.classList.add('input-invalid');
                        attendInput?.setAttribute('title', 'Attendance hours cannot exceed class hours');
                    } else {
                        attendInput?.classList.remove('input-invalid');
                        attendInput?.removeAttribute('title');
                    }

                    const rate = classHours > 0 ? Math.round(attendance / classHours * 100) : 0;
                    totalClass += classHours;
                    totalAttendance += attendance;

                    const rateEl = row.querySelector('[data-rate]');
                    if (rateEl) {
                        rateEl.textContent = rate + '%';
                        if (rate < 80 && classHours > 0) {
                            rateEl.style.color = '#dc2626';
                            rateEl.style.fontWeight = '700';
                        } else if (rate >= 90) {
                            rateEl.style.color = '#15803d';
                            rateEl.style.fontWeight = '600';
                        } else {
                            rateEl.style.color = '';
                            rateEl.style.fontWeight = '';
                        }
                    }
                });

                const overallRate = totalClass > 0 ? Math.round(totalAttendance / totalClass * 100) : 0;

                const totalClassEl = document.querySelector('[data-total-class]');
                const totalAttendEl = document.querySelector('[data-total-attendance]');
                const totalRateEl = document.querySelector('[data-total-rate]');

                if (totalClassEl) totalClassEl.textContent = totalClass;
                if (totalAttendEl) totalAttendEl.textContent = totalAttendance;
                if (totalRateEl) {
                    totalRateEl.textContent = overallRate + '%';
                    if (overallRate < 80 && totalClass > 0) {
                        totalRateEl.style.color = '#dc2626';
                    } else if (overallRate >= 90) {
                        totalRateEl.style.color = '#15803d';
                    } else {
                        totalRateEl.style.color = '';
                    }
                }

                // Update Japanese Immigration (ISA) Threshold badge
                const immBadge = document.getElementById('immigration-badge');
                const immStatus = document.getElementById('immigration-status');

                if (immBadge && immStatus) {
                    if (totalAttendance >= 150) {
                        immBadge.className = 'immigration-threshold-badge met';
                        immStatus.textContent = '✅ ' + totalAttendance + 'h (Meets 150h ISA threshold)';
                    } else {
                        immBadge.className = 'immigration-threshold-badge unmet';
                        const diff = 150 - totalAttendance;
                        immStatus.textContent = '⚠️ ' + totalAttendance + 'h / 150h (' + diff + 'h needed for ISA standard)';
                    }
                }
            };

            const addRow = (data = {}) => {
                const index = nextIndex++;
                const html = template.innerHTML.replaceAll('__INDEX__', String(index));
                rows.insertAdjacentHTML('beforeend', html);
                const row = rows.lastElementChild;

                if (data.year) row.querySelector('[name$="[year]"]').value = data.year;
                if (data.month) row.querySelector('[name$="[month]"]').value = data.month;
                if (data.class_hours !== undefined) row.querySelector('[name$="[class_hours]"]').value = data.class_hours;
                if (data.attendance_hours !== undefined) row.querySelector('[name$="[attendance_hours]"]').value = data.attendance_hours;

                return row;
            };

            // Match 100% Attendance Handler
            if (matchButton) {
                matchButton.addEventListener('click', () => {
                    rows.querySelectorAll('.attendance-row').forEach((row) => {
                        const classInput = row.querySelector('[name$="[class_hours]"]');
                        const attendInput = row.querySelector('[name$="[attendance_hours]"]');
                        if (classInput && attendInput) {
                            attendInput.value = classInput.value;
                        }
                    });
                    refreshTotals();
                });
            }

            // Schedule Presets Handler
            document.querySelectorAll('.preset-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const days = (btn.dataset.days || '').split(',');
                    const hours = btn.dataset.hours;
                    if (hours) {
                        document.getElementById('hours_per_day').value = hours;
                    }
                    form.querySelectorAll('input[name="class_days[]"]').forEach((cb) => {
                        cb.checked = days.includes(cb.value);
                    });
                    syncDaysPerWeek();
                    document.querySelectorAll('.preset-btn').forEach((b) => b.classList.remove('active'));
                    btn.classList.add('active');
                });
            });

            // Holiday Chips Handler
            document.querySelectorAll('.holiday-chip').forEach((chip) => {
                chip.addEventListener('click', () => {
                    const holiday = chip.dataset.holiday;
                    const textarea = document.getElementById('national_holidays');
                    if (!textarea || !holiday) return;
                    const current = textarea.value.trim();
                    if (!current) {
                        textarea.value = holiday;
                    } else if (!current.includes(holiday)) {
                        textarea.value = current + '; ' + holiday;
                    }
                });
            });

            rows.addEventListener('input', (event) => {
                if (event.target.matches('input[type="number"]')) {
                    refreshTotals();
                }
            });

            rows.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');
                if (!button) return;
                if (rows.querySelectorAll('.attendance-row').length > 1) {
                    button.closest('.attendance-row').remove();
                    refreshTotals();
                }
            });

            document.getElementById('add-attendance-row').addEventListener('click', () => {
                addRow().querySelector('input').focus();
            });

            const syncDaysPerWeek = () => {
                const checked = form.querySelectorAll('input[name="class_days[]"]:checked').length;
                if (checked > 0) {
                    document.getElementById('days_per_week').value = checked;
                }
            };

            const syncCheckboxes = (daysPerWeek) => {
                const checkboxes = form.querySelectorAll('input[name="class_days[]"]');
                checkboxes.forEach((cb, i) => {
                    cb.checked = i < daysPerWeek;
                });
                syncDaysPerWeek();
            };

            document.getElementById('course_id').addEventListener('change', (event) => {
                const option = event.target.selectedOptions[0];
                const hours = option?.dataset.hours;
                const days = option?.dataset.days;

                if (hours) document.getElementById('hours_per_day').value = hours;
                if (days) {
                    document.getElementById('days_per_week').value = days;
                    syncCheckboxes(parseInt(days, 10));
                }
            });

            document.getElementById('days_per_week').addEventListener('change', (event) => {
                const val = parseInt(event.target.value, 10);
                if (!isNaN(val) && val >= 1 && val <= 7) {
                    syncCheckboxes(val);
                }
            });

            form.querySelectorAll('input[name="class_days[]"]').forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    syncDaysPerWeek();
                    document.querySelectorAll('.preset-btn').forEach((b) => b.classList.remove('active'));
                });
            });

            syncDaysPerWeek();

            suggestButton.addEventListener('click', async () => {
                suggestError.style.display = 'none';
                suggestButton.disabled = true;
                suggestLabel.textContent = 'Calculating…';

                const payload = {
                    study_start: document.getElementById('study_start').value,
                    study_end: document.getElementById('study_end').value,
                    hours_per_day: document.getElementById('hours_per_day').value,
                    national_holidays: document.getElementById('national_holidays').value,
                    class_days: [...form.querySelectorAll('input[name="class_days[]"]:checked')].map((input) => input.value),
                };

                try {
                    const response = await fetch('<?php echo e(route('admin.certificates.suggest')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const errors = data.errors ? Object.values(data.errors).flat() : [data.message || 'Could not calculate hours.'];
                        throw new Error(errors.join(' '));
                    }

                    if (!data.rows || data.rows.length === 0) {
                        throw new Error('No class days fall inside that study period.');
                    }

                    const existing = new Map();
                    rows.querySelectorAll('.attendance-row').forEach((row) => {
                        const year = row.querySelector('[name$="[year]"]').value;
                        const month = row.querySelector('[name$="[month]"]').value;
                        const attendance = row.querySelector('[name$="[attendance_hours]"]').value;
                        existing.set(year + '-' + month, attendance);
                    });

                    rows.innerHTML = '';
                    nextIndex = 0;

                    data.rows.forEach((row) => {
                        addRow({
                            year: row.year,
                            month: row.month,
                            class_hours: row.class_hours,
                            attendance_hours: existing.get(row.year + '-' + row.month) ?? 0,
                        });
                    });

                    refreshTotals();
                } catch (error) {
                    suggestError.textContent = error.message;
                    suggestError.style.display = 'flex';
                } finally {
                    suggestButton.disabled = false;
                    suggestLabel.textContent = 'Suggest class hours';
                }
            });

            refreshTotals();
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/certificates/form.blade.php ENDPATH**/ ?>