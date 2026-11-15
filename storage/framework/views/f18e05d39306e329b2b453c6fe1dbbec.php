<?php
    $isEdit = $certificate->exists;
    $selectedDays = old('class_days', $certificate->class_days ?? []);
    $selectedDays = is_array($selectedDays) ? $selectedDays : [];
    if (! $isEdit && $selectedDays === []) {
        $selectedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    }

    $selectedInstructors = array_values((array) old('instructors', $certificate->instructors->pluck('id')->all()));
    $selectedInstructors = array_pad($selectedInstructors, 2, '');

    $attendanceRows = old('attendances', $certificate->attendances->map(fn ($a) => [
        'year' => (int) $a->year,
        'month' => (int) $a->month,
        'class_hours' => (int) $a->class_hours,
        'attendance_hours' => (int) $a->attendance_hours,
    ])->all());
    $attendanceRows = is_array($attendanceRows) ? array_values($attendanceRows) : [];

    $initialRows = array_values(array_filter(
        $attendanceRows,
        static fn ($row) => is_array($row) && (int) ($row['class_hours'] ?? 0) > 0,
    ));
    $initialTotalClass = array_sum(array_map(static fn ($row) => (int) ($row['class_hours'] ?? 0), $initialRows));
    $initialTotalAttendance = array_sum(array_map(static fn ($row) => (int) ($row['attendance_hours'] ?? 0), $initialRows));
    $initialRate = $initialTotalClass > 0
        ? (int) round($initialTotalAttendance / $initialTotalClass * 100)
        : 100;
    $initialRate = max(0, min(100, $initialRate));

    $attendanceErrorKey = collect($errors->getBag('default')->keys())
        ->first(fn ($key) => str_starts_with($key, 'attendances'));
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

        <div id="attendance-inputs"></div>

        <nav class="form-steps" aria-label="Certificate sections">
            <a class="form-step" href="#step-1"><span>01</span> Student</a>
            <a class="form-step" href="#step-2"><span>02</span> Course</a>
            <a class="form-step" href="#step-3"><span>03</span> Schedule</a>
            <a class="form-step" href="#step-4"><span>04</span> Summary</a>
            <a class="form-step" href="#step-5"><span>05</span> Sign &amp; issue</a>
        </nav>

        <div class="card" id="step-1">
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
                               autocomplete="off"
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
                               autocomplete="off" lang="ja"
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
                               max="<?php echo e(now()->subDay()->format('Y-m-d')); ?>"
                               value="<?php echo e(old('student_date_of_birth', $certificate->student_date_of_birth?->format('Y-m-d') ?? '')); ?>">
                        <div class="help" id="student-age"></div>
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

                    <div class="field">
                        <label for="student_email">Email <span class="text-muted">(optional, never shown publicly)</span></label>
                        <input id="student_email" class="input" name="student_email" type="email"
                               autocomplete="off"
                               value="<?php echo e(old('student_email', $certificate->student_email ?? '')); ?>">
                        <?php $__errorArgs = ['student_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="student_phone">Phone <span class="text-muted">(optional, never shown publicly)</span></label>
                        <input id="student_phone" class="input" name="student_phone" type="tel"
                               autocomplete="off"
                               value="<?php echo e(old('student_phone', $certificate->student_phone ?? '')); ?>">
                        <?php $__errorArgs = ['student_phone'];
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

        <div class="card" id="step-2">
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
                    <div class="field span-2">
                        <label for="course_id">Course</label>
                        <select id="course_id" class="select" name="course_id" required>
                            <option value="">Select a course…</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->id); ?>"
                                        data-level="<?php echo e($course->level_code); ?>"
                                        data-name="<?php echo e($course->name_en); ?>"
                                        data-nameja="<?php echo e($course->name_ja); ?>"
                                        data-aim="<?php echo e($course->aim_level_en); ?>"
                                        data-book="<?php echo e($course->book_en); ?>"
                                        data-hours="<?php echo e($course->default_hours_per_day); ?>"
                                        data-days="<?php echo e($course->default_days_per_week); ?>"
                                        data-totalhours="<?php echo e($course->total_hours); ?>"
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

                    <div class="course-info span-2" id="course-info" hidden>
                        <div class="course-info-head">
                            <span class="course-code" id="course-code"></span>
                            <div>
                                <strong id="course-name"></strong>
                                <span class="course-name-ja" id="course-name-ja"></span>
                            </div>
                        </div>
                        <dl class="course-meta">
                            <div><dt>Aim level</dt><dd id="course-aim">—</dd></div>
                            <div><dt>Textbook</dt><dd id="course-book">—</dd></div>
                            <div><dt>Total hours</dt><dd id="course-total">—</dd></div>
                            <div><dt>Default schedule</dt><dd id="course-default">—</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" id="step-3">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">03</span>
                    <div>
                        <h2>Study Period &amp; Schedule</h2>
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
                        <div class="help">Filled automatically when you calculate from the course hours.</div>
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
                               inputmode="numeric"
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
                               inputmode="numeric"
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
                            <span class="preset-title">Quick presets</span>
                            <button type="button" class="preset-btn" data-days="Monday,Wednesday,Friday" data-hours="3">Mon, Wed, Fri · 3d × 3h</button>
                            <button type="button" class="preset-btn" data-days="Monday,Tuesday,Wednesday,Thursday,Friday" data-hours="3">Mon–Fri · 5d × 3h</button>
                            <button type="button" class="preset-btn" data-days="Saturday,Sunday" data-hours="4">Sat &amp; Sun · 2d × 4h</button>
                            <button type="button" class="preset-btn" data-days="Saturday,Monday,Wednesday" data-hours="3">Sat, Mon, Wed · 3d × 3h</button>
                        </div>
                        <span class="label">Class days</span>
                        <div class="day-toggles" id="day-toggles">
                            <?php $__currentLoopData = \App\Http\Requests\CertificateRequest::WEEKDAYS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="day-toggle">
                                    <input type="checkbox" name="class_days[]" value="<?php echo e($day); ?>"
                                           <?php if(in_array($day, $selectedDays, true)): echo 'checked'; endif; ?>>
                                    <span><?php echo e($day); ?></span>
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
                        <div class="field-label-row">
                            <label for="national_holidays">Holidays <span class="text-muted">(optional)</span></label>
                            <span class="count-pill" id="holiday-count">0 selected</span>
                        </div>

                        <div class="holiday-layout">
                            <div class="holiday-calendar">
                                <div class="cal-head">
                                    <button type="button" class="cal-nav" id="cal-prev" aria-label="Previous month">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <div class="cal-title">
                                        <strong id="cal-month-label">—</strong>
                                        <span id="cal-sub">Click a date to exclude it</span>
                                    </div>
                                    <button type="button" class="cal-nav" id="cal-next" aria-label="Next month">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>
                                </div>
                                <div class="cal-weekdays" aria-hidden="true">
                                    <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                </div>
                                <div class="cal-grid" id="cal-grid" role="grid" aria-label="Holiday calendar"></div>
                                <div class="cal-legend">
                                    <span><i class="legend-dot is-class"></i> Class day</span>
                                    <span><i class="legend-dot is-holiday"></i> Holiday</span>
                                </div>
                            </div>

                            <div class="holiday-side">
                                <p class="help">Class days are highlighted. Click any date to mark it as a holiday — its scheduled hours are removed from the monthly totals.</p>
                                <div class="holiday-chips" id="holiday-chips"></div>
                                <button type="button" class="btn btn-ghost btn-sm" id="clear-holidays">Clear all holidays</button>

                                <details class="holiday-advanced">
                                    <summary>Edit holidays as text</summary>
                                    <textarea id="national_holidays" class="textarea" name="national_holidays"
                                              placeholder="6月9日; 6月11日; 8月16日; 10月1日&#10;or one Y-m-d date per line"><?php echo e(old('national_holidays', $certificate->national_holidays)); ?></textarea>
                                    <div class="help">Separate dates with semicolons, commas or new lines. Both Japanese dates and Y-m-d work.</div>
                                </details>
                            </div>
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

                    <div class="field span-2">
                        <div class="field-label-row">
                            <label>Course completion</label>
                            <span class="count-pill" id="calc-target-pill">No course selected</span>
                        </div>
                        <div class="calculated-hours-display" id="calculated-hours-display">
                            <div class="calc-grid">
                                <div class="calc-stat">
                                    <span class="label">Target hours</span>
                                    <span class="value mono" id="calc-target-hours">—</span>
                                </div>
                                <div class="calc-stat">
                                    <span class="label">Calculated end date</span>
                                    <span class="value" id="calc-end-date">—</span>
                                </div>
                                <div class="calc-stat">
                                    <span class="label">Months</span>
                                    <span class="value" id="calc-months">—</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-gold" id="calc-end-date-btn">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span>Fit end date to course hours</span>
                            </button>
                            <div id="calc-error" class="error-text" style="display: none;"></div>
                        </div>
                        <div class="help" id="calc-help">Select a course to see its target hours. The end date is fitted automatically from your start date and weekly schedule.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" id="step-4">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">04</span>
                    <div>
                        <h2>Schedule Summary</h2>
                        <p>Generated from the study period and weekly schedule. Monthly hours are written to the certificate automatically.</p>
                    </div>
                </div>
                <div class="card-head-actions">
                    <button type="button" class="btn btn-secondary btn-sm" id="refresh-summary">Recalculate</button>
                </div>
            </div>
            <div class="card-body">
                <div class="summary-toolbar">
                    <div class="field">
                        <label for="attendance_rate">Attendance rate</label>
                        <div class="rate-control">
                            <input id="attendance_rate" class="input" type="number" min="0" max="100" step="1"
                                   inputmode="numeric" value="<?php echo e($initialRate); ?>">
                            <span class="rate-suffix">%</span>
                        </div>
                        <div class="help">Applied to every month. 100% means every class was attended.</div>
                    </div>
                    <div class="summary-totals" aria-live="polite">
                        <div class="summary-total">
                            <span class="label">Class</span>
                            <strong class="mono" id="totals-class">—</strong>
                        </div>
                        <div class="summary-total">
                            <span class="label">Attended</span>
                            <strong class="mono" id="totals-attendance">—</strong>
                        </div>
                        <div class="summary-total">
                            <span class="label">Rate</span>
                            <strong class="mono" id="totals-rate">—</strong>
                        </div>
                    </div>
                </div>

                <div id="summary-status" class="status-banner" hidden></div>

                <div class="schedule-summary" id="schedule-summary" hidden>
                    <div class="summary-grid">
                        <div class="summary-item is-wide">
                            <span class="label">Study period</span>
                            <span class="value" id="summary-period">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Months</span>
                            <span class="value" id="summary-months">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Days / week</span>
                            <span class="value" id="summary-days-week">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Hours / day</span>
                            <span class="value" id="summary-hours-day">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Class days</span>
                            <span class="value value-sm" id="summary-class-days">—</span>
                        </div>
                    </div>

                    <div class="monthly-breakdown">
                        <div class="monthly-breakdown-head">
                            <h4>Monthly breakdown</h4>
                            <span class="text-muted" id="summary-row-count"></span>
                        </div>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="num">Class hours</th>
                                    <th class="num">Attendance</th>
                                </tr>
                                </thead>
                                <tbody id="monthly-breakdown-rows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="summary-error" class="error-text" style="display: none;"></div>
                <div class="help" id="summary-help">Pick a course, start date and class days — the monthly breakdown appears here automatically.</div>

                <?php if($attendanceErrorKey): ?>
                    <div class="error-text"><?php echo e($errors->first($attendanceErrorKey)); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card" id="step-5">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">05</span>
                    <div>
                        <h2>Signatures &amp; Issue Date</h2>
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
                               min="<?php echo e(old('study_end', $certificate->study_end?->format('Y-m-d'))); ?>"
                               value="<?php echo e(old('issued_on', $certificate->issued_on?->format('Y-m-d'))); ?>">
                        <div class="help">Automatically kept on or after the study end date.</div>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <style>
        [hidden] { display: none !important; }

        .form-steps {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: var(--space-5);
        }
        .form-step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-muted-foreground);
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            text-decoration: none;
            transition: border-color var(--duration-fast) var(--ease-out), color var(--duration-fast) var(--ease-out);
        }
        .form-step span {
            font-family: var(--font-mono);
            font-size: 0.6875rem;
            color: var(--color-accent);
        }
        .form-step:hover {
            border-color: var(--color-accent);
            color: var(--color-accent);
        }

        .field-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-3);
            margin-bottom: 6px;
        }
        .field-label-row label { margin-bottom: 0; }
        .count-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 9px;
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--color-muted-foreground);
            background: #F1F5F9;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
        }

        .course-info {
            background: linear-gradient(135deg, #F8FAFC, #FFFFFF);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-4);
        }
        .course-info-head {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-bottom: var(--space-4);
        }
        .course-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 48px;
            padding: 6px 10px;
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 0.8125rem;
            color: var(--color-accent);
            background: var(--color-accent-subtle);
            border: 1px solid #BFDBFE;
            border-radius: var(--radius-sm);
        }
        .course-info-head strong {
            display: block;
            font-size: var(--text-base);
            color: var(--color-foreground);
        }
        .course-name-ja {
            font-size: 0.75rem;
            color: var(--color-muted-foreground);
        }
        .course-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--space-3) var(--space-4);
            margin: 0;
        }
        .course-meta dt {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-muted-foreground);
            margin-bottom: 2px;
        }
        .course-meta dd {
            margin: 0;
            font-size: var(--text-sm);
            font-weight: 600;
            color: var(--color-foreground);
        }

        .day-toggles {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .day-toggle {
            position: relative;
            display: inline-flex;
            cursor: pointer;
            user-select: none;
            margin: 0;
        }
        .day-toggle input {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
        }
        .day-toggle span {
            display: inline-flex;
            align-items: center;
            padding: 7px 14px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            transition: all var(--duration-fast) var(--ease-out);
        }
        .day-toggle:hover span { border-color: var(--color-accent); color: var(--color-accent); }
        .day-toggle input:focus-visible + span { box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
        .day-toggle input:checked + span {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: #FFFFFF;
        }

        .holiday-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
            gap: var(--space-4);
            background: #F8FAFC;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-4);
        }
        .holiday-calendar {
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-3);
        }
        .cal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-2);
            margin-bottom: var(--space-3);
        }
        .cal-title { text-align: center; }
        .cal-title strong {
            display: block;
            font-size: var(--text-sm);
            color: var(--color-foreground);
        }
        .cal-title span {
            display: block;
            font-size: 0.6875rem;
            color: var(--color-muted-foreground);
            margin-top: 1px;
        }
        .cal-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--color-border);
            background: #FFFFFF;
            color: var(--color-muted-foreground);
            cursor: pointer;
            transition: all var(--duration-fast) var(--ease-out);
        }
        .cal-nav:hover { border-color: var(--color-accent); color: var(--color-accent); }
        .cal-weekdays,
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
        }
        .cal-weekdays { margin-bottom: 4px; }
        .cal-weekdays span {
            text-align: center;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-muted-foreground);
            padding: 2px 0;
        }
        .cal-day {
            position: relative;
            aspect-ratio: 1 / 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: var(--font-mono);
            color: #475569;
            background: #FFFFFF;
            border: 1px solid transparent;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all var(--duration-fast) var(--ease-out);
        }
        .cal-day:hover { background: #F1F5F9; }
        .cal-day.is-empty { visibility: hidden; cursor: default; }
        .cal-day.is-class { background: var(--color-accent-subtle); color: var(--color-accent); }
        .cal-day.is-class:hover { background: #DBEAFE; }
        .cal-day.is-holiday {
            background: var(--color-destructive);
            border-color: var(--color-destructive);
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
        }
        .cal-day.is-holiday:hover { background: #DC2626; }
        .cal-day.is-today { outline: 2px solid var(--color-gold); outline-offset: 1px; }
        .cal-legend {
            display: flex;
            gap: var(--space-3);
            margin-top: var(--space-3);
            padding-top: var(--space-2);
            border-top: 1px dashed var(--color-border);
            font-size: 0.6875rem;
            color: var(--color-muted-foreground);
        }
        .cal-legend span { display: inline-flex; align-items: center; gap: 5px; }
        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            display: inline-block;
        }
        .legend-dot.is-class { background: var(--color-accent-subtle); border: 1px solid #BFDBFE; }
        .legend-dot.is-holiday { background: var(--color-destructive); }

        .holiday-side {
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
            min-width: 0;
        }
        .holiday-side .help { margin: 0; }
        .holiday-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .holiday-chips .chip-empty {
            font-size: 0.75rem;
            color: var(--color-muted-foreground);
            font-style: italic;
        }
        .holiday-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 5px 4px 10px;
            font-size: 0.72rem;
            font-weight: 600;
            background: var(--color-destructive-bg);
            color: var(--color-destructive-text);
            border: 1px solid var(--color-destructive-border);
            border-radius: var(--radius-full);
        }
        .holiday-chip button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: none;
            background: rgba(153, 27, 27, 0.1);
            color: var(--color-destructive-text);
            cursor: pointer;
            font-size: 0.9rem;
            line-height: 1;
        }
        .holiday-chip button:hover { background: var(--color-destructive); color: #FFFFFF; }
        .holiday-advanced {
            border-top: 1px dashed var(--color-border);
            padding-top: var(--space-3);
        }
        .holiday-advanced summary {
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-muted-foreground);
        }
        .holiday-advanced summary:hover { color: var(--color-accent); }
        .holiday-advanced .textarea { margin-top: var(--space-2); min-height: 84px; }

        .calculated-hours-display {
            background: var(--color-bg-elevated, #F8FAFC);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-4);
        }
        .calc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: var(--space-3);
            margin-bottom: var(--space-4);
        }
        .calc-stat { display: flex; flex-direction: column; gap: 2px; }
        .calc-stat .label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-muted-foreground);
        }
        .calc-stat .value {
            font-size: var(--text-base);
            font-weight: 700;
            color: var(--color-foreground);
        }
        .calculated-hours-display .btn { width: 100%; }

        .summary-toolbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: var(--space-6);
            flex-wrap: wrap;
            margin-bottom: var(--space-4);
        }
        .summary-toolbar .field { margin-bottom: 0; min-width: 200px; }
        .rate-control {
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        .rate-control .input { width: 100px; font-family: var(--font-mono); }
        .rate-suffix {
            font-size: var(--text-sm);
            font-weight: 700;
            color: var(--color-muted-foreground);
        }
        .summary-totals {
            display: flex;
            gap: var(--space-3);
            flex-wrap: wrap;
        }
        .summary-total {
            min-width: 108px;
            padding: var(--space-3) var(--space-4);
            background: #F8FAFC;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
        }
        .summary-total .label {
            display: block;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-muted-foreground);
            margin-bottom: 2px;
        }
        .summary-total strong {
            font-size: var(--text-lg);
            color: var(--color-foreground);
            font-variant-numeric: tabular-nums;
        }

        .status-banner {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-md);
            font-size: var(--text-sm);
            margin-bottom: var(--space-4);
            border: 1px solid transparent;
        }
        .status-banner strong { font-weight: 700; }
        .status-banner .btn { margin-left: auto; }
        .status-banner.is-ok {
            background: var(--color-success-bg);
            border-color: var(--color-success-border);
            color: var(--color-success-text);
        }
        .status-banner.is-warn {
            background: var(--color-warning-bg);
            border-color: var(--color-warning-border);
            color: var(--color-warning-text);
        }
        .status-banner.is-danger {
            background: var(--color-destructive-bg);
            border-color: var(--color-destructive-border);
            color: var(--color-destructive-text);
        }
        .status-banner.is-info {
            background: var(--color-info-bg);
            border-color: var(--color-info-border);
            color: var(--color-info-text);
        }

        .schedule-summary {
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-4);
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: var(--space-4);
            margin-bottom: var(--space-5);
            padding-bottom: var(--space-4);
            border-bottom: 1px solid var(--color-border);
        }
        .summary-item { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .summary-item.is-wide { grid-column: span 2; }
        .summary-item .label {
            color: var(--color-muted-foreground);
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .summary-item .value {
            font-weight: 700;
            font-size: var(--text-base);
            color: var(--color-foreground);
        }
        .summary-item .value.mono { font-family: var(--font-mono); }
        .summary-item .value-sm { font-size: var(--text-sm); font-weight: 600; }
        .monthly-breakdown-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: var(--space-3);
            margin-bottom: var(--space-2);
        }
        .monthly-breakdown h4 {
            font-size: var(--text-sm);
            font-weight: 700;
            color: var(--color-foreground);
            margin: 0;
        }
        .monthly-breakdown .table-wrap { max-height: 320px; overflow-y: auto; }
        .monthly-breakdown table { width: 100%; }
        .monthly-breakdown td, .monthly-breakdown th { font-variant-numeric: tabular-nums; }

        .spinner {
            width: 13px;
            height: 13px;
            border: 2px solid rgba(255, 255, 255, 0.45);
            border-top-color: #FFFFFF;
            border-radius: 50%;
            display: inline-block;
            animation: spin 0.7s linear infinite;
            margin-right: 6px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 960px) {
            .holiday-layout { grid-template-columns: minmax(0, 1fr); }
            .summary-item.is-wide { grid-column: span 1; }
        }
        @media (max-width: 720px) {
            .form-grid { grid-template-columns: minmax(0, 1fr); }
            .form-grid .span-2 { grid-column: auto; }
            .summary-toolbar { flex-direction: column; }
        }
    </style>

    <script>
        (() => {
            const form = document.getElementById('certificate-form');
            if (!form) return;

            const $ = (id) => document.getElementById(id);
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const routes = {
                suggest: '<?php echo e(route('admin.certificates.suggest')); ?>',
                recalc: '<?php echo e(route('admin.certificates.calculate-end-date')); ?>',
            };

            const startInput = $('study_start');
            const endInput = $('study_end');
            const hoursInput = $('hours_per_day');
            const daysInput = $('days_per_week');
            const holidayInput = $('national_holidays');
            const courseSelect = $('course_id');
            const issuedInput = $('issued_on');
            const rateInput = $('attendance_rate');
            const hiddenInputs = $('attendance-inputs');

            const summary = $('schedule-summary');
            const summaryHelp = $('summary-help');
            const summaryError = $('summary-error');
            const statusEl = $('summary-status');
            const monthlyRows = $('monthly-breakdown-rows');

            const WEEKDAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            const WEEKDAY_JA = { Monday: '月', Tuesday: '火', Wednesday: '水', Thursday: '木', Friday: '金', Saturday: '土', Sunday: '日' };

            const initialRows = <?php echo json_encode($initialRows, 15, 512) ?>;
            const isEdit = <?php echo json_encode($isEdit, 15, 512) ?>;
            const initialRate = <?php echo e($initialRate); ?>;

            let rows = (Array.isArray(initialRows) ? initialRows : [])
                .map((row) => ({
                    year: Number(row.year),
                    month: Number(row.month),
                    class_hours: Number(row.class_hours) || 0,
                    attendance_hours: Number(row.attendance_hours) || 0,
                }))
                .filter((row) => row.class_hours > 0)
                .sort((a, b) => (a.year - b.year) || (a.month - b.month));

            let holidays = new Set();
            let calCursor = new Date();
            let refreshTimer = null;
            let issuedDirty = false;

            const pad = (value) => String(value).padStart(2, '0');
            const isoOf = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
            const parseDate = (value) => (value ? new Date(value + 'T00:00:00') : null);

            const formatDate = (value) => {
                const date = parseDate(value);
                if (!date || Number.isNaN(date.getTime())) return '—';
                return `${date.getFullYear()}年${date.getMonth() + 1}月${date.getDate()}日`;
            };

            const formatMonth = (year, month) => `${year}年${pad(month)}月`;

            const selectedDays = () => [...form.querySelectorAll('input[name="class_days[]"]:checked')]
                .map((input) => input.value);

            const currentRate = () => {
                const value = parseInt(rateInput.value, 10);
                if (Number.isNaN(value)) return 100;
                return Math.max(0, Math.min(100, value));
            };

            const targetHours = () => {
                const option = courseSelect.selectedOptions[0];
                const value = Number(option?.dataset?.totalhours || 0);
                return Number.isFinite(value) ? value : 0;
            };

            // ------------------------------------------------------------------
            // Holidays
            // ------------------------------------------------------------------

            const studyYears = () => {
                const start = parseDate(startInput.value) ?? new Date();
                const end = parseDate(endInput.value) ?? start;
                const first = Math.min(start.getFullYear(), end.getFullYear());
                const last = Math.max(start.getFullYear(), end.getFullYear());
                const years = [];
                for (let year = first; year <= last; year++) years.push(year);
                if (!years.includes(last + 1) && years.length < 3) years.push(last + 1);
                return years;
            };

            const parseHolidayText = (text) => {
                const set = new Set();
                if (!text) return set;
                const years = studyYears();

                text.split(/[;,\n]+/u).forEach((raw) => {
                    const part = raw.trim();
                    if (!part) return;

                    let match = part.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
                    if (match) {
                        set.add(`${match[1]}-${pad(match[2])}-${pad(match[3])}`);
                        return;
                    }

                    match = part.match(/^(\d{1,2})月(\d{1,2})日$/u);
                    if (match) {
                        years.forEach((year) => set.add(`${year}-${pad(match[1])}-${pad(match[2])}`));
                        return;
                    }

                    const parsed = new Date(part);
                    if (!Number.isNaN(parsed.getTime())) {
                        set.add(isoOf(parsed));
                    }
                });

                return set;
            };

            const writeHolidays = () => {
                holidayInput.value = [...holidays].sort().join('; ');
            };

            const renderHolidayChips = () => {
                const container = $('holiday-chips');
                container.innerHTML = '';
                const dates = [...holidays].sort();

                $('holiday-count').textContent = dates.length === 1
                    ? '1 selected'
                    : `${dates.length} selected`;

                if (dates.length === 0) {
                    const empty = document.createElement('span');
                    empty.className = 'chip-empty';
                    empty.textContent = 'No holidays selected.';
                    container.appendChild(empty);
                    return;
                }

                dates.forEach((date) => {
                    const chip = document.createElement('span');
                    chip.className = 'holiday-chip';
                    const label = document.createElement('span');
                    label.textContent = formatDate(date);
                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.dataset.removeHoliday = date;
                    remove.setAttribute('aria-label', `Remove ${date}`);
                    remove.textContent = '×';
                    chip.append(label, remove);
                    container.appendChild(chip);
                });
            };

            const renderCalendar = () => {
                const year = calCursor.getFullYear();
                const month = calCursor.getMonth();

                $('cal-month-label').textContent = `${year}年 ${month + 1}月`;
                const weekdays = selectedDays().length;
                $('cal-sub').textContent = weekdays > 0
                    ? `${weekdays} class ${weekdays === 1 ? 'day' : 'days'} per week`
                    : 'Select class days to highlight them';

                const grid = $('cal-grid');
                grid.innerHTML = '';

                const first = new Date(year, month, 1);
                const offset = (first.getDay() + 6) % 7;
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const classDays = selectedDays();
                const today = isoOf(new Date());

                for (let i = 0; i < offset; i++) {
                    const filler = document.createElement('span');
                    filler.className = 'cal-day is-empty';
                    grid.appendChild(filler);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = new Date(year, month, day);
                    const value = isoOf(date);
                    const dayName = WEEKDAYS[(date.getDay() + 6) % 7];
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'cal-day';
                    button.textContent = String(day);
                    button.dataset.date = value;
                    button.title = `${value} · ${dayName}`;

                    if (classDays.includes(dayName)) button.classList.add('is-class');
                    if (holidays.has(value)) {
                        button.classList.add('is-holiday');
                        button.setAttribute('aria-pressed', 'true');
                    }
                    if (value === today) button.classList.add('is-today');

                    button.addEventListener('click', () => toggleHoliday(value));
                    grid.appendChild(button);
                }
            };

            const toggleHoliday = (date) => {
                if (holidays.has(date)) {
                    holidays.delete(date);
                } else {
                    holidays.add(date);
                }
                writeHolidays();
                renderCalendar();
                renderHolidayChips();
                scheduleRefresh();
            };

            const syncCalendarToStart = () => {
                const start = parseDate(startInput.value);
                if (start) calCursor = new Date(start.getFullYear(), start.getMonth(), 1);
            };

            // ------------------------------------------------------------------
            // Summary rendering
            // ------------------------------------------------------------------

            const renderHiddenInputs = () => {
                hiddenInputs.innerHTML = '';
                rows.forEach((row, index) => {
                    ['year', 'month', 'class_hours', 'attendance_hours'].forEach((key) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `attendances[${index}][${key}]`;
                        input.value = row[key];
                        hiddenInputs.appendChild(input);
                    });
                });
            };

            const renderSummary = () => {
                const classDays = selectedDays();
                const totalClass = rows.reduce((sum, row) => sum + row.class_hours, 0);
                const totalAttendance = rows.reduce((sum, row) => sum + row.attendance_hours, 0);
                const rate = totalClass > 0 ? Math.round((totalAttendance / totalClass) * 100) : 0;
                const target = targetHours();
                const months = rows.length;

                $('totals-class').textContent = totalClass + ' h';
                $('totals-attendance').textContent = totalAttendance + ' h';
                $('totals-rate').textContent = rate + '%';

                $('calc-months').textContent = months > 0 ? `${months} month${months === 1 ? '' : 's'}` : '—';
                $('calc-end-date').textContent = formatDate(endInput.value);
                $('calc-target-hours').textContent = target > 0 ? target + ' h' : '—';

                if (months === 0) {
                    summary.hidden = true;
                    summaryHelp.hidden = false;
                    statusEl.hidden = true;
                    return;
                }

                summary.hidden = false;
                summaryHelp.hidden = true;

                $('summary-period').textContent = `${formatDate(startInput.value)} → ${formatDate(endInput.value)}`;
                $('summary-months').textContent = String(months);
                $('summary-days-week').textContent = String(classDays.length);
                $('summary-hours-day').textContent = `${hoursInput.value || 0} h`;
                $('summary-class-days').textContent = classDays.map((day) => WEEKDAY_JA[day]).join('・');
                $('summary-row-count').textContent = `${months} showing`;

                monthlyRows.innerHTML = '';
                rows.forEach((row) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${formatMonth(row.year, row.month)}</td>
                        <td class="num">${row.class_hours}</td>
                        <td class="num">${row.attendance_hours}</td>
                    `;
                    monthlyRows.appendChild(tr);
                });

                renderStatus(target, totalClass, months);
            };

            const renderStatus = (target, totalClass, months) => {
                statusEl.hidden = false;
                statusEl.className = 'status-banner';
                statusEl.innerHTML = '';

                if (months > 24) {
                    statusEl.classList.add('is-danger');
                    const message = document.createElement('span');
                    message.innerHTML = '<strong>Too many months.</strong> A certificate can hold at most 24 monthly attendance rows. Tighten the study period.';
                    statusEl.appendChild(message);
                    return;
                }

                if (target <= 0) {
                    statusEl.classList.add('is-info');
                    const message = document.createElement('span');
                    message.textContent = 'Select a course to compare the generated hours against its curriculum.';
                    statusEl.appendChild(message);
                    return;
                }

                const difference = totalClass - target;

                if (difference === 0) {
                    statusEl.classList.add('is-ok');
                    const message = document.createElement('span');
                    message.innerHTML = `<strong>${totalClass} h matches the course curriculum.</strong> The schedule is ready to issue.`;
                    statusEl.appendChild(message);
                    return;
                }

                statusEl.classList.add('is-warn');
                const message = document.createElement('span');
                const hint = difference < 0
                    ? `${Math.abs(difference)} h short of`
                    : `${difference} h over`;
                message.innerHTML = `<strong>${totalClass} h of ${target} h.</strong> ${hint} the curriculum — fit the end date to correct it.`;
                statusEl.appendChild(message);

                const fit = document.createElement('button');
                fit.type = 'button';
                fit.className = 'btn btn-secondary btn-sm';
                fit.textContent = `Fit end date to ${target} h`;
                fit.addEventListener('click', () => runRefresh(true));
                statusEl.appendChild(fit);
            };

            const renderAll = () => {
                renderHiddenInputs();
                renderSummary();
            };

            const applyRate = () => {
                const rate = currentRate();
                rows = rows.map((row) => ({
                    ...row,
                    attendance_hours: Math.max(0, Math.min(row.class_hours, Math.round(row.class_hours * rate / 100))),
                }));
                renderAll();
            };

            // ------------------------------------------------------------------
            // Server calls
            // ------------------------------------------------------------------

            const postJson = async (url, payload) => {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(payload),
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (error) {
                    data = {};
                }

                if (!response.ok) {
                    const errors = data.errors ? Object.values(data.errors).flat() : [data.message || 'Request failed.'];
                    throw new Error(errors.join(' '));
                }

                return data;
            };

            const showError = (message) => {
                summaryError.textContent = message;
                summaryError.style.display = 'flex';
            };

            const clearError = () => {
                summaryError.textContent = '';
                summaryError.style.display = 'none';
            };

            const withBusy = async (button, busyLabel, task) => {
                if (!button) return task();
                const original = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `<span class="spinner"></span>${busyLabel}`;
                try {
                    return await task();
                } finally {
                    button.disabled = false;
                    button.innerHTML = original;
                }
            };

            const runRefresh = async (recalculateEnd = false) => {
                clearError();

                const start = startInput.value;
                const classDays = selectedDays();
                const hours = Number(hoursInput.value || 0);
                const button = recalculateEnd ? $('calc-end-date-btn') : $('refresh-summary');

                if (!start) {
                    showError('Enter the study start date first.');
                    return;
                }
                if (!classDays.length) {
                    showError('Select at least one class day.');
                    return;
                }
                if (!hours) {
                    showError('Enter how many hours are taught per day.');
                    return;
                }

                await withBusy(button, 'Calculating…', async () => {
                    try {
                        let end = endInput.value;

                        if (recalculateEnd) {
                            const target = targetHours();
                            if (!target) {
                                throw new Error('Select a course so the target hours are known.');
                            }

                            const result = await postJson(routes.recalc, {
                                study_start: start,
                                target_hours: target,
                                class_days: classDays,
                                hours_per_day: hours,
                                national_holidays: holidayInput.value,
                            });

                            end = result.study_end;
                            endInput.value = end;
                            syncCalendarToStart();
                            renderCalendar();

                            if (!isEdit && !issuedDirty) {
                                const today = isoOf(new Date());
                                issuedInput.value = end > today ? end : today;
                            }
                        }

                        if (!end) {
                            throw new Error('Enter the study end date.');
                        }

                        const result = await postJson(routes.suggest, {
                            study_start: start,
                            study_end: end,
                            class_days: classDays,
                            hours_per_day: hours,
                            national_holidays: holidayInput.value,
                        });

                        const rate = currentRate();
                        rows = (result.rows || []).map((row) => {
                            const classHours = Number(row.class_hours) || 0;
                            return {
                                year: Number(row.year),
                                month: Number(row.month),
                                class_hours: classHours,
                                attendance_hours: Math.max(0, Math.min(classHours, Math.round(classHours * rate / 100))),
                            };
                        });

                        syncIssuedOn();
                        renderAll();
                    } catch (error) {
                        showError(error.message || 'Could not build the schedule.');
                    }
                });
            };

            const scheduleRefresh = () => {
                if (!courseSelect.value) return;
                if (!startInput.value || !endInput.value) return;
                if (!selectedDays().length || !Number(hoursInput.value || 0)) return;

                clearTimeout(refreshTimer);
                refreshTimer = setTimeout(() => {
                    runRefresh(false);
                }, 400);
            };

            // ------------------------------------------------------------------
            // Date helpers
            // ------------------------------------------------------------------

            const syncIssuedOn = () => {
                if (!issuedInput) return;
                const end = endInput.value;
                issuedInput.min = end || '';
                if (!issuedInput.value || (end && issuedInput.value < end)) {
                    issuedInput.value = end || issuedInput.value;
                }
            };

            const updateStudentAge = () => {
                const input = $('student_date_of_birth');
                const target = $('student-age');
                if (!input || !target) return;

                if (!input.value) {
                    target.textContent = '';
                    return;
                }

                const birth = parseDate(input.value);
                if (!birth || Number.isNaN(birth.getTime())) {
                    target.textContent = '';
                    return;
                }

                const today = new Date();
                let age = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                    age--;
                }

                target.textContent = age >= 0 ? `Age: ${age} years` : '';
            };

            // ------------------------------------------------------------------
            // Course info
            // ------------------------------------------------------------------

            const updateCourseInfo = () => {
                const info = $('course-info');
                const option = courseSelect.selectedOptions[0];

                if (!courseSelect.value || !option) {
                    info.hidden = true;
                    $('calc-target-pill').textContent = 'No course selected';
                    $('calc-target-hours').textContent = '—';
                    $('calculated-hours-display').hidden = true;
                    $('calc-help').hidden = false;
                    return;
                }

                info.hidden = false;
                $('course-code').textContent = option.dataset.level || '—';
                $('course-name').textContent = option.dataset.name || '—';
                $('course-name-ja').textContent = option.dataset.nameja || '';
                $('course-aim').textContent = option.dataset.aim || '—';
                $('course-book').textContent = option.dataset.book || '—';
                $('course-total').textContent = option.dataset.totalhours ? `${option.dataset.totalhours} h` : '—';
                $('course-default').textContent = option.dataset.hours && option.dataset.days
                    ? `${option.dataset.hours} h/day · ${option.dataset.days} days/week`
                    : '—';

                const target = targetHours();
                $('calc-target-pill').textContent = target > 0 ? `${target} h curriculum` : 'No target hours';
                $('calc-target-hours').textContent = target > 0 ? `${target} h` : '—';
                $('calculated-hours-display').hidden = target <= 0;
                $('calc-help').hidden = target > 0;
            };

            // ------------------------------------------------------------------
            // Day helpers
            // ------------------------------------------------------------------

            const syncDaysPerWeek = () => {
                const checked = form.querySelectorAll('input[name="class_days[]"]:checked').length;
                if (checked > 0) daysInput.value = checked;
            };

            const syncCheckboxes = (count) => {
                const checkboxes = [...form.querySelectorAll('input[name="class_days[]"]')];
                checkboxes.forEach((checkbox, index) => {
                    checkbox.checked = index < count;
                });
                syncDaysPerWeek();
            };

            const markPreset = (activeButton) => {
                document.querySelectorAll('.preset-btn').forEach((button) => button.classList.remove('active'));
                if (activeButton) activeButton.classList.add('active');
            };

            // ------------------------------------------------------------------
            // Events
            // ------------------------------------------------------------------

            rateInput.addEventListener('change', applyRate);
            rateInput.addEventListener('blur', applyRate);

            startInput.addEventListener('change', () => {
                syncCalendarToStart();
                renderCalendar();
                scheduleRefresh();
            });

            endInput.addEventListener('change', () => {
                syncIssuedOn();
                renderCalendar();
                scheduleRefresh();
            });

            hoursInput.addEventListener('change', () => {
                renderSummary();
                scheduleRefresh();
            });

            daysInput.addEventListener('change', () => {
                const value = parseInt(daysInput.value, 10);
                if (!Number.isNaN(value) && value >= 1 && value <= 7) {
                    syncCheckboxes(value);
                    renderCalendar();
                    scheduleRefresh();
                }
            });

            form.querySelectorAll('input[name="class_days[]"]').forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    syncDaysPerWeek();
                    markPreset(null);
                    renderCalendar();
                    scheduleRefresh();
                });
            });

            document.querySelectorAll('.preset-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    const days = (button.dataset.days || '').split(',').filter(Boolean);
                    const hours = button.dataset.hours;

                    if (hours) hoursInput.value = hours;
                    form.querySelectorAll('input[name="class_days[]"]').forEach((checkbox) => {
                        checkbox.checked = days.includes(checkbox.value);
                    });

                    syncDaysPerWeek();
                    markPreset(button);
                    renderCalendar();
                    scheduleRefresh();
                });
            });

            courseSelect.addEventListener('change', () => {
                const option = courseSelect.selectedOptions[0];
                const hours = option?.dataset?.hours;
                const days = option?.dataset?.days;

                if (hours) hoursInput.value = hours;
                if (days) {
                    daysInput.value = days;
                    syncCheckboxes(parseInt(days, 10));
                }

                updateCourseInfo();
                renderCalendar();

                if (courseSelect.value) {
                    runRefresh(!isEdit);
                }
            });

            holidayInput.addEventListener('input', () => {
                holidays = parseHolidayText(holidayInput.value);
                renderCalendar();
                renderHolidayChips();
                scheduleRefresh();
            });

            $('holiday-chips').addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-holiday]');
                if (!button) return;
                toggleHoliday(button.dataset.removeHoliday);
            });

            $('clear-holidays').addEventListener('click', () => {
                holidays.clear();
                writeHolidays();
                renderCalendar();
                renderHolidayChips();
                scheduleRefresh();
            });

            $('cal-prev').addEventListener('click', () => {
                calCursor = new Date(calCursor.getFullYear(), calCursor.getMonth() - 1, 1);
                renderCalendar();
            });

            $('cal-next').addEventListener('click', () => {
                calCursor = new Date(calCursor.getFullYear(), calCursor.getMonth() + 1, 1);
                renderCalendar();
            });

            $('calc-end-date-btn').addEventListener('click', () => runRefresh(true));
            $('refresh-summary').addEventListener('click', () => runRefresh(false));

            $('student_date_of_birth').addEventListener('input', updateStudentAge);

            issuedInput.addEventListener('input', () => {
                issuedDirty = true;
            });

            form.addEventListener('submit', (event) => {
                if (rows.length === 0) {
                    event.preventDefault();
                    showError('Generate the schedule before saving. Pick a course and valid study dates.');
                    document.getElementById('step-4').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            // ------------------------------------------------------------------
            // Initialise
            // ------------------------------------------------------------------

            rateInput.value = initialRate;
            holidays = parseHolidayText(holidayInput.value);
            syncCalendarToStart();
            syncIssuedOn();
            updateStudentAge();
            updateCourseInfo();
            renderCalendar();
            renderHolidayChips();
            renderAll();

            if (rows.length === 0 && courseSelect.value && startInput.value && endInput.value && selectedDays().length) {
                runRefresh(false);
            }
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/certificates/form.blade.php ENDPATH**/ ?>