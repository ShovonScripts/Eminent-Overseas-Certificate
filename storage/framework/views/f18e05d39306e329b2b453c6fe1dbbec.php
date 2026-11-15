<?php
    $isEdit = $certificate->exists;
    $selectedDays = old('class_days', $certificate->class_days ?? []);
    $selectedDays = is_array($selectedDays) ? $selectedDays : [];
    if (! $isEdit && $selectedDays === []) {
        $selectedDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday'];
    }

    $orderedWeekdays = [
        'Saturday',
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
    ];

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

        <!-- Sticky Stepper Navigation -->
        <div class="stepper-sticky-container">
            <div class="stepper-progress-track">
                <div class="stepper-progress-fill" id="stepper-progress-fill" style="width: 20%;"></div>
            </div>
            <nav class="form-steps" aria-label="Certificate sections">
                <a class="form-step active" href="#step-1" id="nav-step-1">
                    <span class="step-num">01</span>
                    <span class="step-label">Student</span>
                    <span class="step-check" id="step-1-chk">✓</span>
                </a>
                <a class="form-step" href="#step-2" id="nav-step-2">
                    <span class="step-num">02</span>
                    <span class="step-label">Course</span>
                    <span class="step-check" id="step-2-chk">✓</span>
                </a>
                <a class="form-step" href="#step-3" id="nav-step-3">
                    <span class="step-num">03</span>
                    <span class="step-label">Schedule &amp; Holidays</span>
                    <span class="step-check" id="step-3-chk">✓</span>
                </a>
                <a class="form-step" href="#step-4" id="nav-step-4">
                    <span class="step-num">04</span>
                    <span class="step-label">Attendance Summary</span>
                    <span class="step-check" id="step-4-chk">✓</span>
                </a>
                <a class="form-step" href="#step-5" id="nav-step-5">
                    <span class="step-num">05</span>
                    <span class="step-label">Sign &amp; Issue</span>
                    <span class="step-check" id="step-5-chk">✓</span>
                </a>
            </nav>
        </div>

        <!-- Step 1: Student Information (All in English) -->
        <div class="card form-section-card" id="step-1">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">01</span>
                    <div>
                        <h2>Student Personal Information</h2>
                        <p>Enter the student identity details as required for official certificate records.</p>
                    </div>
                </div>
                <?php if($isEdit): ?>
                    <span class="badge badge-draft">Draft #<?php echo e($certificate->id); ?></span>
                <?php else: ?>
                    <span class="badge badge-info">New Draft</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="student_name_en">Student Name (English) <span class="req-star">*</span></label>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <input id="student_name_en" class="input" name="student_name_en" type="text" required
                                   autocomplete="off" placeholder="e.g. Md. Anik Rahman"
                                   value="<?php echo e(old('student_name_en', $certificate->student_name_en ?? '')); ?>">
                        </div>
                        <div class="help">As shown in passport / NID. Words auto-capitalize on blur.</div>
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
                        <label for="student_name_ja">Student Name in Japanese <span class="text-muted">(Optional)</span></label>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="2" x2="22" y1="12" y2="12"/>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            <input id="student_name_ja" class="input" name="student_name_ja" type="text"
                                   autocomplete="off" lang="ja" placeholder="e.g. Japanese Katakana name"
                                   value="<?php echo e(old('student_name_ja', $certificate->student_name_ja ?? '')); ?>">
                        </div>
                        <div class="help">Optional Katakana spelling for Japanese immigration and embassy verification.</div>
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
                        <div class="field-label-row">
                            <label for="student_date_of_birth">Date of Birth <span class="req-star">*</span></label>
                            <span class="age-pill" id="student-age-pill" style="display: none;"></span>
                        </div>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                <line x1="16" x2="16" y1="2" y2="6"/>
                                <line x1="8" x2="8" y1="2" y2="6"/>
                                <line x1="3" x2="21" y1="10" y2="10"/>
                            </svg>
                            <input id="student_date_of_birth" class="input" name="student_date_of_birth" type="date" required
                                   max="<?php echo e(now()->subDay()->format('Y-m-d')); ?>"
                                   value="<?php echo e(old('student_date_of_birth', $certificate->student_date_of_birth?->format('Y-m-d') ?? '')); ?>">
                        </div>
                        <div class="help" id="student-age">Format: YYYY-MM-DD. Age is calculated automatically.</div>
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
                        <label for="student_gender">Gender <span class="req-star">*</span></label>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="m4.93 4.93 4.24 4.24"/>
                                <path d="m14.83 9.17 4.24-4.24"/>
                                <path d="M14 5h5v5"/>
                            </svg>
                            <select id="student_gender" class="select" name="student_gender" required>
                                <option value="">Select gender…</option>
                                <?php $__currentLoopData = \App\Enums\Gender::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gender->value); ?>" <?php if(old('student_gender', $certificate->student_gender?->value) === $gender->value): echo 'selected'; endif; ?>>
                                        <?php echo e($gender->label()); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php $__errorArgs = ['student_gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <?php if($isEdit): ?>
                        <input type="hidden" name="student_email" value="<?php echo e(old('student_email', $certificate->student_email ?? '')); ?>">
                        <input type="hidden" name="student_phone" value="<?php echo e(old('student_phone', $certificate->student_phone ?? '')); ?>">
                    <?php endif; ?>
                </div>

                <div class="step-card-footer">
                    <button type="button" class="btn btn-secondary next-step-btn" data-next="step-2">
                        <span>Continue to Course Curriculum</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Course Curriculum (All in English) -->
        <div class="card form-section-card" id="step-2">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">02</span>
                    <div>
                        <h2>Course Curriculum</h2>
                        <p>Select the accredited Japanese language curriculum and target completion hours.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field span-2">
                        <label for="course_id">Course <span class="req-star">*</span></label>
                        <select id="course_id" class="select select-lg" name="course_id" required>
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
                                    <?php echo e($course->name_en); ?> (<?php echo e($course->level_code); ?>) — <?php echo e($course->total_hours); ?> Hours Curriculum
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
                            <span class="course-code" id="course-code">JLPT</span>
                            <div class="course-info-titles">
                                <strong id="course-name">Course Name</strong>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="apply-course-defaults-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                <span>Apply Course Schedule Defaults</span>
                            </button>
                        </div>
                        <dl class="course-meta">
                            <div class="meta-item">
                                <dt>Aim Level</dt>
                                <dd id="course-aim">—</dd>
                            </div>
                            <div class="meta-item">
                                <dt>Accredited Textbook</dt>
                                <dd id="course-book">—</dd>
                            </div>
                            <div class="meta-item">
                                <dt>Total Target Hours</dt>
                                <dd><span class="badge-total-hours" id="course-total">—</span></dd>
                            </div>
                            <div class="meta-item">
                                <dt>Recommended Batch</dt>
                                <dd id="course-default">—</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="step-card-footer">
                    <button type="button" class="btn btn-ghost prev-step-btn" data-prev="step-1">&larr; Back</button>
                    <button type="button" class="btn btn-secondary next-step-btn" data-next="step-3">
                        <span>Continue to Schedule &amp; Holidays</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Study Period & Schedule (Friday Government Holiday Feature, All in English) -->
        <div class="card form-section-card" id="step-3">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">03</span>
                    <div>
                        <h2>Study Period &amp; Schedule</h2>
                        <p>Dates, weekly schedule, and calendar exclusions. Friday is the official weekly government holiday.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Bangladesh Government Holiday Banner -->
                <div class="govt-holiday-banner" role="region" aria-label="Official weekly holiday policy">
                    <div class="govt-holiday-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                            <path d="M8 14h.01"/>
                            <path d="M12 14h.01"/>
                            <path d="M16 14h.01"/>
                        </svg>
                    </div>
                    <div class="govt-holiday-text">
                        <div class="banner-title-row">
                            <strong>Government Holiday Notice: Friday is the Only Weekly Government Holiday</strong>
                            <span class="banner-tag">Weekly Off: Friday</span>
                        </div>
                        <p>In accordance with Bangladesh government regulations, <strong>Friday is the designated weekly government holiday</strong>. Classes run from Saturday through Thursday. Friday is excluded from standard schedules.</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <div class="field-label-row">
                            <label for="study_start">Study Start Date <span class="req-star">*</span></label>
                            <div class="start-date-quick-chips">
                                <button type="button" class="btn btn-ghost btn-xs start-chip" data-start-type="this-month-1st">1st of Month</button>
                                <button type="button" class="btn btn-ghost btn-xs start-chip" data-start-type="next-month-1st">Next Month</button>
                                <button type="button" class="btn btn-ghost btn-xs start-chip" data-start-type="today">Today</button>
                            </div>
                        </div>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 2v4"/><path d="M16 2v4"/></svg>
                            <input id="study_start" class="input" name="study_start" type="date" required
                                   value="<?php echo e(old('study_start', $certificate->study_start?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="help">First day of scheduled classroom sessions.</div>
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
                        <div class="field-label-row">
                            <label for="study_end">Study End Date <span class="req-star">*</span></label>
                            <div class="end-date-quick-chips">
                                <button type="button" class="btn btn-ghost btn-xs session-nudge" data-nudge="-1" title="Subtract 1 class session (-3h)">-1 Class</button>
                                <button type="button" class="btn btn-ghost btn-xs session-nudge" data-nudge="1" title="Add 1 class session (+3h)">+1 Class</button>
                                <span class="auto-fit-pill" id="end-date-status-pill">180h Target</span>
                            </div>
                        </div>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 2v4"/><path d="M16 2v4"/></svg>
                            <input id="study_end" class="input" name="study_end" type="date" required
                                   value="<?php echo e(old('study_end', $certificate->study_end?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="help">Fitted automatically to match the standard 180-hour curriculum.</div>
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
                        <label for="hours_per_day">Hours Per Day <span class="req-star">*</span></label>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <input id="hours_per_day" class="input" name="hours_per_day" type="number" min="1" max="12" required
                                   inputmode="numeric" placeholder="3"
                                   value="<?php echo e(old('hours_per_day', $certificate->hours_per_day ?? 3)); ?>">
                        </div>
                        <div class="help">Standard 3 hours per class session.</div>
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
                        <label for="days_per_week">Days Per Week <span class="req-star">*</span></label>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                            <input id="days_per_week" class="input" name="days_per_week" type="number" min="1" max="7" required
                                   inputmode="numeric" placeholder="5"
                                   value="<?php echo e(old('days_per_week', $certificate->days_per_week ?? 5)); ?>">
                        </div>
                        <div class="help">Automatically synced with selected class days.</div>
                        <?php $__errorArgs = ['days_per_week'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="error-text"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Standard 180-Hour Presets (Friday is Excluded) -->
                    <div class="field span-2">
                        <div class="field-label-row">
                            <label>Standard 180-Hour Batch Presets</label>
                            <span class="count-pill">Click any preset to auto-fit 180 hours &middot; Friday is excluded</span>
                        </div>
                        <div class="schedule-presets-grid" id="schedule-presets">
                            <button type="button" class="preset-card-btn active" data-days="Saturday,Sunday,Monday,Tuesday,Wednesday" data-hours="3" data-batch="3m-intensive">
                                <span class="preset-title">3-Month Intensive (180h)</span>
                                <span class="preset-meta">5 days/wk (Sat–Wed) &middot; 3h/day &middot; 60 Sessions</span>
                                <span class="preset-tag">12 Weeks &middot; Standard N5</span>
                            </button>
                            <button type="button" class="preset-card-btn" data-days="Saturday,Monday,Wednesday" data-hours="3" data-batch="4.5m-std">
                                <span class="preset-title">4.5-Month Regular (180h)</span>
                                <span class="preset-meta">3 days/wk (Sat, Mon, Wed) &middot; 3h/day &middot; 60 Sessions</span>
                                <span class="preset-tag">20 Weeks &middot; Most Popular</span>
                            </button>
                            <button type="button" class="preset-card-btn" data-days="Sunday,Tuesday,Thursday" data-hours="3" data-batch="4.5m-alt">
                                <span class="preset-title">4.5-Month Alternate (180h)</span>
                                <span class="preset-meta">3 days/wk (Sun, Tue, Thu) &middot; 3h/day &middot; 60 Sessions</span>
                                <span class="preset-tag">20 Weeks &middot; Alternate Days</span>
                            </button>
                            <button type="button" class="preset-card-btn" data-days="Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday" data-hours="3" data-batch="2.5m-fast">
                                <span class="preset-title">2.5-Month Fast-Track (180h)</span>
                                <span class="preset-meta">6 days/wk (Sat–Thu, No Fri) &middot; 3h/day &middot; 60 Sessions</span>
                                <span class="preset-tag">10 Weeks &middot; Super Intensive</span>
                            </button>
                            <button type="button" class="preset-card-btn" data-days="Saturday,Sunday" data-hours="4" data-batch="5m-weekend">
                                <span class="preset-title">5-Month Weekend (180h)</span>
                                <span class="preset-meta">2 days/wk (Sat &amp; Sun) &middot; 4h/day &middot; 45 Sessions</span>
                                <span class="preset-tag">22.5 Weeks &middot; Weekend</span>
                            </button>
                        </div>
                    </div>

                    <!-- Weekday Selector (Starts on Saturday; Friday clearly badged as Government Holiday) -->
                    <div class="field span-2">
                        <div class="field-label-row">
                            <label>Class Days <span class="req-star">*</span></label>
                            <span class="days-selected-count" id="days-count-badge">5 days selected</span>
                        </div>
                        <div class="day-toggles-container">
                            <div class="day-toggles" id="day-toggles">
                                <?php $__currentLoopData = $orderedWeekdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $isFriday = ($day === 'Friday');
                                    ?>
                                    <label class="day-toggle <?php echo e($isFriday ? 'is-friday-toggle' : ''); ?>">
                                        <input type="checkbox" name="class_days[]" value="<?php echo e($day); ?>"
                                               id="day-cb-<?php echo e(strtolower($day)); ?>"
                                               <?php if(in_array($day, $selectedDays, true)): echo 'checked'; endif; ?>>
                                        <span class="day-toggle-pill">
                                            <span class="day-name"><?php echo e($day); ?></span>
                                            <?php if($isFriday): ?>
                                                <span class="govt-holiday-pill-mini">Govt Holiday</span>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <!-- Dynamic Friday Warning Note -->
                        <div class="friday-selection-warning" id="friday-warning" style="display: none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                            <div>
                                <strong>Note on Friday Selection:</strong> Friday is designated as the official weekly government holiday. Classes are generally not held on Fridays unless this batch has special authorized weekend-holiday hours.
                            </div>
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

                    <!-- Interactive Calendar & Holiday Exclusions (All in English) -->
                    <div class="field span-2">
                        <div class="field-label-row">
                            <div>
                                <label for="national_holidays">Holidays &amp; Exclusions <span class="text-muted">(Optional)</span></label>
                                <div class="help" style="margin-top: 2px;">Fridays are automatically non-class days. Use this calendar to exclude other public or national holidays from class hours.</div>
                            </div>
                            <span class="count-pill" id="holiday-count">0 excluded</span>
                        </div>

                        <div class="holiday-layout">
                            <div class="holiday-calendar">
                                <div class="cal-head">
                                    <button type="button" class="cal-nav" id="cal-prev" aria-label="Previous month">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <div class="cal-title">
                                        <strong id="cal-month-label">—</strong>
                                        <span id="cal-sub">Click any date to exclude or reinstate</span>
                                    </div>
                                    <button type="button" class="cal-nav" id="cal-next" aria-label="Next month">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>
                                </div>
                                <div class="cal-shortcuts">
                                    <button type="button" class="btn btn-ghost btn-xs" id="jump-to-start-btn">Jump to start month</button>
                                    <button type="button" class="btn btn-ghost btn-xs" id="jump-to-end-btn">Jump to end month</button>
                                </div>
                                <div class="cal-weekdays" aria-hidden="true">
                                    <span>Sat</span>
                                    <span>Sun</span>
                                    <span>Mon</span>
                                    <span>Tue</span>
                                    <span>Wed</span>
                                    <span>Thu</span>
                                    <span class="cal-fri-header" title="Weekly Government Holiday">Fri <small>Off</small></span>
                                </div>
                                <div class="cal-grid" id="cal-grid" role="grid" aria-label="Holiday calendar"></div>
                                <div class="cal-legend">
                                    <span><i class="legend-dot is-class"></i> Class Day</span>
                                    <span><i class="legend-dot is-friday"></i> Friday (Govt Holiday)</span>
                                    <span><i class="legend-dot is-holiday"></i> Excluded Holiday</span>
                                    <span><i class="legend-dot is-today-dot"></i> Today</span>
                                </div>
                            </div>

                            <div class="holiday-side">
                                <div class="holiday-quick-add-section">
                                    <span class="quick-add-title">Quick add Bangladesh national holidays:</span>
                                    <div class="quick-holiday-chips">
                                        <button type="button" class="quick-add-chip" data-bd-holiday="02-21" title="International Mother Language Day">+ 21 Feb (Language Day)</button>
                                        <button type="button" class="quick-add-chip" data-bd-holiday="03-26" title="Independence Day of Bangladesh">+ 26 Mar (Independence)</button>
                                        <button type="button" class="quick-add-chip" data-bd-holiday="04-14" title="Bengali New Year">+ 14 Apr (Pohela Boishakh)</button>
                                        <button type="button" class="quick-add-chip" data-bd-holiday="05-01" title="May Day">+ 01 May (Labour Day)</button>
                                        <button type="button" class="quick-add-chip" data-bd-holiday="12-16" title="Victory Day of Bangladesh">+ 16 Dec (Victory Day)</button>
                                    </div>
                                </div>

                                <div class="chips-container-header">
                                    <span class="label-muted">Excluded holiday dates:</span>
                                    <button type="button" class="btn btn-ghost btn-xs" id="clear-holidays">Clear all</button>
                                </div>
                                <div class="holiday-chips" id="holiday-chips"></div>

                                <details class="holiday-advanced">
                                    <summary>Manual text input (YYYY-MM-DD)</summary>
                                    <textarea id="national_holidays" class="textarea" name="national_holidays"
                                              placeholder="2025-02-21; 2025-03-26; 2025-12-16"><?php echo e(old('national_holidays', $certificate->national_holidays)); ?></textarea>
                                    <div class="help">Separate dates with semicolons, commas or new lines. Formats like YYYY-MM-DD are supported.</div>
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

                    <!-- 180-Hour Curriculum Compliance Gauge & Target Alignment Engine -->
                    <div class="field span-2">
                        <div class="field-label-row">
                            <label>180-Hour Curriculum Standard &amp; Alignment</label>
                            <span class="count-pill" id="calc-target-pill">180h Curriculum</span>
                        </div>
                        <div class="calculated-hours-display target-180-compliance-card" id="calculated-hours-display">
                            <div class="compliance-header-row">
                                <div class="compliance-title-group">
                                    <span class="compliance-title">Official Standard: <strong>180 Hours</strong> (MOJ &amp; Immigration Requirement)</span>
                                    <span class="compliance-badge is-ok" id="compliance-status-badge">Checking schedule…</span>
                                </div>
                                <div class="compliance-sessions-counter" id="compliance-sessions-counter">
                                    60 / 60 Sessions
                                </div>
                            </div>

                            <div class="compliance-bar-track">
                                <div class="compliance-bar-fill" id="compliance-progress-bar" style="width: 100%;"></div>
                            </div>

                            <div class="calc-grid">
                                <div class="calc-stat">
                                    <span class="label">Curriculum Target</span>
                                    <span class="value mono" id="calc-target-hours">180 h</span>
                                </div>
                                <div class="calc-stat">
                                    <span class="label">Scheduled Class Hours</span>
                                    <span class="value mono" id="calc-current-hours">—</span>
                                </div>
                                <div class="calc-stat">
                                    <span class="label">Calculated End Date</span>
                                    <span class="value" id="calc-end-date">—</span>
                                </div>
                                <div class="calc-stat">
                                    <span class="label">Total Study Months</span>
                                    <span class="value" id="calc-months">—</span>
                                </div>
                            </div>

                            <div class="compliance-action-row">
                                <button type="button" class="btn btn-gold btn-fit-end" id="calc-end-date-btn">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>Fit end date to course hours</span>
                                </button>
                                <label class="auto-compensate-label" title="Automatically extends study end date when holidays are added so the student always completes exactly 180 hours">
                                    <input type="checkbox" id="auto-compensate-holidays" checked>
                                    <span>Auto-compensate holidays to lock 180h target</span>
                                </label>
                            </div>
                            <div id="calc-error" class="error-text" style="display: none;"></div>
                        </div>
                        <div class="help" id="calc-help">Calculates the exact final class date that reaches the standard 180 hours, automatically skipping Friday weekly government holidays and calendar exclusions.</div>
                    </div>
                </div>

                <div class="step-card-footer">
                    <button type="button" class="btn btn-ghost prev-step-btn" data-prev="step-2">&larr; Back</button>
                    <button type="button" class="btn btn-secondary next-step-btn" data-next="step-4">
                        <span>Continue to Attendance Summary</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 4: Schedule Summary & Monthly Breakdown (All in English) -->
        <div class="card form-section-card" id="step-4">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">04</span>
                    <div>
                        <h2>Schedule Summary &amp; Attendance Breakdown</h2>
                        <p>Computed from study period and weekly schedule. Monthly attendance hours print automatically.</p>
                    </div>
                </div>
                <div class="card-head-actions">
                    <button type="button" class="btn btn-secondary btn-sm" id="refresh-summary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                        <span>Recalculate</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="summary-toolbar">
                    <div class="field">
                        <label for="attendance_rate">Attendance rate</label>
                        <div class="rate-control-group">
                            <div class="rate-input-wrap">
                                <input id="attendance_rate" class="input" type="number" min="0" max="100" step="1"
                                       inputmode="numeric" value="<?php echo e($initialRate); ?>">
                                <span class="rate-suffix">%</span>
                            </div>
                            <input id="attendance_rate_range" class="rate-slider" type="range" min="50" max="100" step="1" value="<?php echo e($initialRate); ?>">
                        </div>
                        <div class="rate-preset-chips">
                            <button type="button" class="rate-chip" data-rate="100">100% (Full)</button>
                            <button type="button" class="rate-chip" data-rate="95">95% (High)</button>
                            <button type="button" class="rate-chip" data-rate="90">90%</button>
                            <button type="button" class="rate-chip" data-rate="85">85%</button>
                        </div>
                        <div class="help">Applied to compute monthly attended hours. 100% means all classes attended.</div>
                    </div>

                    <div class="summary-totals-cards" aria-live="polite">
                        <div class="summary-kpi-card">
                            <span class="kpi-label">Class Hours</span>
                            <strong class="kpi-value mono" id="totals-class">—</strong>
                            <span class="kpi-sub" id="totals-class-sub">Target: —</span>
                        </div>
                        <div class="summary-kpi-card">
                            <span class="kpi-label">Attended Hours</span>
                            <strong class="kpi-value mono" id="totals-attendance">—</strong>
                            <span class="kpi-sub">Actual presence</span>
                        </div>
                        <div class="summary-kpi-card">
                            <span class="kpi-label">Attendance Rate</span>
                            <strong class="kpi-value mono" id="totals-rate">—</strong>
                            <span class="kpi-sub" id="totals-rate-status">Ready</span>
                        </div>
                    </div>
                </div>

                <div id="summary-status" class="status-banner" hidden></div>

                <div class="schedule-summary" id="schedule-summary" hidden>
                    <div class="summary-grid">
                        <div class="summary-item is-wide">
                            <span class="label">Study Period</span>
                            <span class="value" id="summary-period">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Duration</span>
                            <span class="value" id="summary-months">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Weekly Schedule</span>
                            <span class="value" id="summary-days-week">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Daily Schedule</span>
                            <span class="value" id="summary-hours-day">—</span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Class Days</span>
                            <span class="value value-sm" id="summary-class-days">—</span>
                        </div>
                    </div>

                    <div class="monthly-breakdown">
                        <div class="monthly-breakdown-head">
                            <div>
                                <h4>Monthly Attendance Ledger</h4>
                                <p class="help" style="margin: 0;">Breakdown by year and month for certificate records.</p>
                            </div>
                            <span class="badge badge-info" id="summary-row-count"></span>
                        </div>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="num">Class Hours</th>
                                    <th class="num">Attended Hours</th>
                                    <th class="num">Attendance Rate</th>
                                </tr>
                                </thead>
                                <tbody id="monthly-breakdown-rows"></tbody>
                                <tfoot id="monthly-breakdown-foot">
                                    <tr class="table-total-row">
                                        <td><strong>Total Summary</strong></td>
                                        <td class="num"><strong id="foot-total-class">—</strong></td>
                                        <td class="num"><strong id="foot-total-attendance">—</strong></td>
                                        <td class="num"><strong id="foot-total-rate">—</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="summary-error" class="error-text" style="display: none;"></div>
                <div class="help" id="summary-help">Select a course, study start date, and class days — monthly hours will calculate automatically.</div>

                <?php if($attendanceErrorKey): ?>
                    <div class="error-text"><?php echo e($errors->first($attendanceErrorKey)); ?></div>
                <?php endif; ?>

                <div class="step-card-footer">
                    <button type="button" class="btn btn-ghost prev-step-btn" data-prev="step-3">&larr; Back</button>
                    <button type="button" class="btn btn-secondary next-step-btn" data-next="step-5">
                        <span>Continue to Signatures &amp; Issue</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 5: Signatures & Issue Date (All in English) -->
        <div class="card form-section-card" id="step-5">
            <div class="card-head">
                <div class="card-head-title">
                    <span class="step-badge">05</span>
                    <div>
                        <h2>Signatures &amp; Date of Issue</h2>
                        <p>Designate certifying faculty and official date of certificate issuance.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="instructor_1">Primary Instructor <span class="req-star">* (Left Signature)</span></label>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <select id="instructor_1" class="select select-lg" name="instructors[]" required>
                                <option value="">Select primary instructor / director…</option>
                                <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($instructor->id); ?>" <?php if((string) $selectedInstructors[0] === (string) $instructor->id): echo 'selected'; endif; ?>>
                                        <?php echo e($instructor->name); ?> &middot; <?php echo e($instructor->title); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="help">Appears as the main authorized instructor on the left side.</div>
                    </div>

                    <div class="field">
                        <label for="instructor_2">Secondary Instructor <span class="text-muted">(Right Signature, Optional)</span></label>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <select id="instructor_2" class="select select-lg" name="instructors[]">
                                <option value="">No second instructor (single signatory)</option>
                                <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($instructor->id); ?>" <?php if((string) $selectedInstructors[1] === (string) $instructor->id): echo 'selected'; endif; ?>>
                                        <?php echo e($instructor->name); ?> &middot; <?php echo e($instructor->title); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="help">Optional second instructor/seal on the right side.</div>
                    </div>

                    <div class="field span-2">
                        <div class="field-label-row">
                            <label for="issued_on">Issue Date <span class="req-star">*</span></label>
                            <div class="issue-date-shortcuts">
                                <button type="button" class="btn btn-ghost btn-xs" id="issue-match-end">Same as study end</button>
                                <button type="button" class="btn btn-ghost btn-xs" id="issue-today">Today</button>
                            </div>
                        </div>
                        <div class="input-with-icon">
                            <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 2v4"/><path d="M16 2v4"/></svg>
                            <input id="issued_on" class="input" name="issued_on" type="date" required
                                   min="<?php echo e(old('study_end', $certificate->study_end?->format('Y-m-d'))); ?>"
                                   value="<?php echo e(old('issued_on', $certificate->issued_on?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="help">Must be on or after the study end date. Stamped on the certificate seal.</div>
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

                <!-- Ready to Save Checklist (All in English) -->
                <div class="form-ready-checklist" id="form-ready-checklist">
                    <span class="checklist-title">Pre-Submission Verification:</span>
                    <div class="checklist-items">
                        <span class="checklist-item" id="chk-item-student">
                            <i class="chk-icon">✓</i> Student Information
                        </span>
                        <span class="checklist-item" id="chk-item-course">
                            <i class="chk-icon">✓</i> Course Selected
                        </span>
                        <span class="checklist-item" id="chk-item-schedule">
                            <i class="chk-icon">✓</i> Friday Holiday Respected
                        </span>
                        <span class="checklist-item" id="chk-item-hours">
                            <i class="chk-icon">✓</i> Hours Matched
                        </span>
                        <span class="checklist-item" id="chk-item-sign">
                            <i class="chk-icon">✓</i> Primary Instructor
                        </span>
                    </div>
                </div>

                <div class="form-actions-bar">
                    <div class="actions-left">
                        <a class="btn btn-ghost" href="<?php echo e($isEdit ? route('admin.certificates.show', $certificate) : route('admin.certificates.index')); ?>">
                            Cancel &amp; Discard
                        </a>
                    </div>
                    <div class="actions-right">
                        <button class="btn btn-primary btn-lg" type="submit" id="btn-submit-draft">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            <span><?php echo e($isEdit ? 'Update Draft Certificate' : 'Save as Draft Certificate'); ?></span>
                            <kbd class="btn-kbd">Ctrl+S</kbd>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <style>
        [hidden] { display: none !important; }

        /* ========== Sticky Stepper Navigation ========== */
        .stepper-sticky-container {
            position: sticky;
            top: 64px;
            z-index: 40;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 8px 12px;
            margin-bottom: var(--space-6);
            box-shadow: var(--shadow-sm);
        }
        .stepper-progress-track {
            height: 3px;
            background: #E2E8F0;
            border-radius: var(--radius-full);
            overflow: hidden;
            margin-bottom: 8px;
        }
        .stepper-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--color-accent), #38BDF8);
            border-radius: var(--radius-full);
            transition: width 0.3s ease;
        }
        .form-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            overflow-x: auto;
            padding: 2px 0;
            scrollbar-width: none;
        }
        .form-steps::-webkit-scrollbar { display: none; }
        .form-step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--color-muted-foreground);
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            text-decoration: none;
            white-space: nowrap;
            transition: all var(--duration-fast) var(--ease-out);
        }
        .form-step .step-num {
            font-family: var(--font-mono);
            font-size: 0.6875rem;
            color: var(--color-muted-foreground);
            background: #F1F5F9;
            padding: 1px 6px;
            border-radius: var(--radius-full);
        }
        .form-step .step-check {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #10B981;
            color: #FFFFFF;
            font-size: 0.625rem;
            font-weight: 800;
            opacity: 0;
            transform: scale(0.6);
            transition: all var(--duration-fast) var(--ease-out);
        }
        .form-step.is-complete .step-check {
            opacity: 1;
            transform: scale(1);
        }
        .form-step:hover {
            border-color: var(--color-accent);
            color: var(--color-accent);
        }
        .form-step.active {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: #FFFFFF;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }
        .form-step.active .step-num {
            background: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
        }

        /* ========== Card Polish ========== */
        .form-section-card {
            margin-bottom: var(--space-6);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-border);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            scroll-margin-top: 140px;
        }
        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            font-family: var(--font-mono);
            font-size: 0.9375rem;
            font-weight: 800;
            color: #FFFFFF;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            box-shadow: 0 2px 6px rgba(30, 58, 138, 0.3);
            flex-shrink: 0;
        }
        .step-card-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: var(--space-3);
            margin-top: var(--space-5);
            padding-top: var(--space-4);
            border-top: 1px solid var(--color-border-subtle);
        }
        .req-star {
            color: var(--color-destructive);
            font-weight: bold;
        }

        /* ========== Field Polish ========== */
        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-with-icon .field-icon {
            position: absolute;
            left: 12px;
            width: 17px;
            height: 17px;
            color: var(--color-muted-foreground);
            pointer-events: none;
            transition: color var(--duration-fast);
        }
        .input-with-icon .input,
        .input-with-icon .select {
            padding-left: 38px;
        }
        .input-with-icon:focus-within .field-icon {
            color: var(--color-accent);
        }
        .select-lg {
            font-size: 0.9375rem;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .age-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: var(--radius-full);
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .auto-fit-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 9px;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #0369A1;
            background: #E0F2FE;
            border: 1px solid #BAE6FD;
            border-radius: var(--radius-full);
            transition: all var(--duration-fast);
        }
        .auto-fit-pill.is-ok {
            background: #DCFCE7;
            color: #15803D;
            border-color: #86EFAC;
        }
        .auto-fit-pill.is-warn {
            background: #FEF3C7;
            color: #B45309;
            border-color: #FDE68A;
        }
        .start-date-quick-chips,
        .end-date-quick-chips {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }
        .start-chip,
        .session-nudge {
            padding: 2px 8px;
            font-size: 0.6875rem;
            font-weight: 600;
            height: auto;
            border-radius: var(--radius-full);
            background: #F1F5F9;
            color: #334155;
            border: 1px solid var(--color-border);
            cursor: pointer;
            transition: all var(--duration-fast);
            line-height: 1.4;
        }
        .start-chip:hover,
        .session-nudge:hover {
            background: var(--color-accent-subtle);
            color: var(--color-accent);
            border-color: #BFDBFE;
        }
        .session-nudge:active {
            transform: scale(0.95);
        }

        /* ========== Government Holiday Banner ========== */
        .govt-holiday-banner {
            display: flex;
            align-items: flex-start;
            gap: var(--space-4);
            background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
            border: 1px solid #FCD34D;
            border-radius: var(--radius-md);
            padding: var(--space-4);
            margin-bottom: var(--space-5);
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.08);
        }
        .govt-holiday-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            background: #F59E0B;
            color: #FFFFFF;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.35);
        }
        .govt-holiday-text {
            flex: 1;
            min-width: 0;
        }
        .banner-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-2);
            flex-wrap: wrap;
            margin-bottom: 4px;
        }
        .banner-title-row strong {
            font-size: 0.9375rem;
            color: #78350F;
            font-weight: 700;
        }
        .banner-tag {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: var(--radius-full);
            background: #B45309;
            color: #FFFFFF;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .govt-holiday-text p {
            margin: 0;
            font-size: 0.8125rem;
            line-height: 1.5;
            color: #92400E;
        }

        /* ========== Curated Presets Grid ========== */
        .schedule-presets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: var(--space-2);
            margin-bottom: var(--space-3);
        }
        .preset-card-btn {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 10px 14px;
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            cursor: pointer;
            text-align: left;
            transition: all var(--duration-fast) var(--ease-out);
        }
        .preset-card-btn:hover {
            border-color: var(--color-accent);
            background: #F8FAFC;
            transform: translateY(-1px);
        }
        .preset-card-btn.active {
            border-color: var(--color-accent);
            background: var(--color-accent-subtle);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        .preset-card-btn .preset-title {
            font-size: 0.84375rem;
            font-weight: 700;
            color: var(--color-foreground);
        }
        .preset-card-btn.active .preset-title {
            color: var(--color-accent);
        }
        .preset-card-btn .preset-meta {
            font-size: 0.72rem;
            color: var(--color-muted-foreground);
            margin-top: 2px;
        }
        .preset-card-btn .preset-tag {
            display: inline-block;
            margin-top: 6px;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0369A1;
            background: #E0F2FE;
            padding: 1px 6px;
            border-radius: var(--radius-xs);
        }

        /* ========== Weekday Selector (Friday Highlighted) ========== */
        .day-toggles-container {
            background: #F8FAFC;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-3);
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
        .day-toggle-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            transition: all var(--duration-fast) var(--ease-out);
        }
        .day-toggle:hover .day-toggle-pill {
            border-color: var(--color-accent);
            color: var(--color-accent);
        }
        .day-toggle input:focus-visible + .day-toggle-pill {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }
        .day-toggle input:checked + .day-toggle-pill {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        }
        /* Friday Specific Styling */
        .day-toggle.is-friday-toggle .day-toggle-pill {
            border-color: #FCD34D;
            background: #FFFBEB;
            color: #92400E;
        }
        .govt-holiday-pill-mini {
            display: inline-block;
            font-size: 0.625rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 1px 6px;
            border-radius: var(--radius-full);
            background: #FDE68A;
            color: #78350F;
        }
        .day-toggle.is-friday-toggle input:checked + .day-toggle-pill {
            background: #D97706;
            border-color: #B45309;
            color: #FFFFFF;
        }
        .day-toggle.is-friday-toggle input:checked + .day-toggle-pill .govt-holiday-pill-mini {
            background: rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
        }

        .friday-selection-warning {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 10px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            background: #FEF3C7;
            border: 1px solid #FDE68A;
            color: #92400E;
            font-size: 0.8125rem;
            line-height: 1.45;
        }
        .friday-selection-warning svg {
            flex-shrink: 0;
            color: #D97706;
            margin-top: 1px;
        }
        .days-selected-count {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            font-size: 0.6875rem;
            font-weight: 700;
            color: var(--color-accent);
            background: var(--color-accent-subtle);
            border: 1px solid #BFDBFE;
            border-radius: var(--radius-full);
        }

        /* ========== Holiday Layout & Calendar (All in English) ========== */
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }
        .cal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-2);
            margin-bottom: var(--space-2);
        }
        .cal-title { text-align: center; }
        .cal-title strong {
            display: block;
            font-size: var(--text-base);
            color: var(--color-foreground);
            font-weight: 700;
        }
        .cal-title span {
            display: block;
            font-size: 0.6875rem;
            color: var(--color-muted-foreground);
            margin-top: 1px;
        }
        .cal-shortcuts {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: var(--space-3);
            padding-bottom: 6px;
            border-bottom: 1px dashed var(--color-border);
        }
        .btn-xs {
            padding: 2px 8px;
            font-size: 0.6875rem;
            height: auto;
        }
        .cal-weekdays,
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
        }
        .cal-weekdays { margin-bottom: 6px; }
        .cal-weekdays span {
            text-align: center;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-muted-foreground);
            padding: 3px 0;
        }
        .cal-weekdays .cal-fri-header {
            color: #B45309;
            background: #FEF3C7;
            border-radius: var(--radius-xs);
        }
        .cal-weekdays .cal-fri-header small {
            display: block;
            font-size: 0.5625rem;
            font-weight: 800;
        }
        .cal-day {
            position: relative;
            aspect-ratio: 1 / 1;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: var(--font-mono);
            color: #475569;
            background: #FFFFFF;
            border: 1px solid var(--color-border-subtle);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all var(--duration-fast) var(--ease-out);
        }
        .cal-day:hover {
            background: #F1F5F9;
            border-color: var(--color-accent);
        }
        .cal-day.is-empty { visibility: hidden; cursor: default; }
        /* Class Day */
        .cal-day.is-class {
            background: var(--color-accent-subtle);
            border-color: #BFDBFE;
            color: var(--color-accent);
            font-weight: 700;
        }
        .cal-day.is-class:hover { background: #DBEAFE; }
        /* Friday Weekly Holiday */
        .cal-day.is-friday-holiday {
            background: #FEF3C7;
            border-color: #FDE68A;
            color: #92400E;
        }
        .cal-day.is-friday-holiday:hover { background: #FDE68A; }
        /* Excluded Holiday */
        .cal-day.is-holiday {
            background: var(--color-destructive);
            border-color: var(--color-destructive);
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35);
        }
        .cal-day.is-holiday:hover { background: #DC2626; }
        .cal-day.is-today {
            outline: 2px solid var(--color-gold);
            outline-offset: 1px;
        }
        .cal-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 14px;
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
        .legend-dot.is-friday { background: #FEF3C7; border: 1px solid #FCD34D; }
        .legend-dot.is-holiday { background: var(--color-destructive); }
        .legend-dot.is-today-dot { background: #FFFFFF; border: 2px solid var(--color-gold); }

        /* Holiday Side & Quick Add */
        .holiday-side {
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
            min-width: 0;
        }
        .holiday-quick-add-section {
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            padding: 10px 12px;
        }
        .quick-add-title {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-muted-foreground);
            margin-bottom: 6px;
        }
        .quick-holiday-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .quick-add-chip {
            padding: 3px 8px;
            font-size: 0.6875rem;
            font-weight: 600;
            background: #F1F5F9;
            color: #334155;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all var(--duration-fast);
        }
        .quick-add-chip:hover {
            background: var(--color-accent-subtle);
            color: var(--color-accent);
            border-color: #BFDBFE;
        }
        .chips-container-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .label-muted {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--color-muted-foreground);
        }
        .holiday-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            min-height: 32px;
        }
        .holiday-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 6px 3px 10px;
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
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: none;
            background: rgba(153, 27, 27, 0.1);
            color: var(--color-destructive-text);
            cursor: pointer;
            font-size: 0.85rem;
            line-height: 1;
        }
        .holiday-chip button:hover { background: var(--color-destructive); color: #FFFFFF; }
        .holiday-advanced {
            border-top: 1px dashed var(--color-border);
            padding-top: var(--space-2);
        }
        .holiday-advanced summary {
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-muted-foreground);
        }
        .holiday-advanced summary:hover { color: var(--color-accent); }

        /* ========== Course Info Display ========== */
        .course-info {
            background: linear-gradient(135deg, #F8FAFC, #FFFFFF);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-4);
            box-shadow: var(--shadow-sm);
        }
        .course-info-head {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-bottom: var(--space-4);
            flex-wrap: wrap;
        }
        .course-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            padding: 6px 12px;
            font-family: var(--font-mono);
            font-weight: 800;
            font-size: 0.9375rem;
            color: var(--color-accent);
            background: var(--color-accent-subtle);
            border: 1px solid #BFDBFE;
            border-radius: var(--radius-sm);
        }
        .course-info-titles {
            flex: 1;
            min-width: 200px;
        }
        .course-info-titles strong {
            display: block;
            font-size: var(--text-base);
            color: var(--color-foreground);
        }
        .course-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: var(--space-3);
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
        .badge-total-hours {
            display: inline-block;
            padding: 2px 8px;
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
            border-radius: var(--radius-xs);
            font-family: var(--font-mono);
            font-weight: 700;
        }

        /* ========== 180-Hour Compliance Card & Calculated Display ========== */
        .calculated-hours-display {
            background: #F8FAFC;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--space-4);
        }
        .target-180-compliance-card {
            background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
            border: 1px solid #CBD5E1;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            position: relative;
        }
        .compliance-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-3);
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .compliance-title-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .compliance-title {
            font-size: 0.8125rem;
            color: var(--color-foreground);
        }
        .compliance-title strong {
            color: var(--color-primary);
        }
        .compliance-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: var(--radius-full);
            background: #F1F5F9;
            color: #475569;
            border: 1px solid var(--color-border);
            transition: all var(--duration-fast);
        }
        .compliance-badge.is-ok {
            background: #DCFCE7;
            color: #15803D;
            border-color: #86EFAC;
        }
        .compliance-badge.is-warn {
            background: #FEF3C7;
            color: #B45309;
            border-color: #FDE68A;
        }
        .compliance-badge.is-info {
            background: #E0E7FF;
            color: #4338CA;
            border-color: #C7D2FE;
        }
        .compliance-badge.is-danger {
            background: #FEE2E2;
            color: #B91C1C;
            border-color: #FCA5A5;
        }
        .compliance-sessions-counter {
            font-family: var(--font-mono);
            font-size: 0.78125rem;
            font-weight: 800;
            color: var(--color-primary);
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            padding: 3px 10px;
            border-radius: var(--radius-sm);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        .compliance-bar-track {
            width: 100%;
            height: 8px;
            background: #E2E8F0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .compliance-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #3B82F6, #10B981);
            transition: width 0.35s ease, background 0.35s ease;
            border-radius: 4px;
        }
        .calc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
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
        .compliance-action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-4);
            flex-wrap: wrap;
            margin-top: 10px;
            padding-top: 12px;
            border-top: 1px dashed var(--color-border);
        }
        .btn-fit-end {
            flex: 1;
            min-width: 240px;
            width: auto;
            justify-content: center;
            font-weight: 700;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .auto-compensate-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            user-select: none;
        }
        .auto-compensate-label input[type="checkbox"] {
            accent-color: var(--color-accent);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        /* ========== Summary Toolbar & KPI Cards ========== */
        .summary-toolbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: var(--space-6);
            flex-wrap: wrap;
            margin-bottom: var(--space-4);
        }
        .rate-control-group {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-top: 6px;
        }
        .rate-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 96px;
        }
        .rate-input-wrap .input {
            font-family: var(--font-mono);
            font-weight: 700;
            padding-right: 24px;
        }
        .rate-input-wrap .rate-suffix {
            position: absolute;
            right: 10px;
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--color-muted-foreground);
            pointer-events: none;
        }
        .rate-slider {
            flex: 1;
            accent-color: var(--color-accent);
            cursor: pointer;
        }
        .rate-preset-chips {
            display: flex;
            gap: 6px;
            margin-top: 8px;
        }
        .rate-chip {
            padding: 2px 8px;
            font-size: 0.6875rem;
            font-weight: 600;
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all var(--duration-fast);
        }
        .rate-chip:hover {
            border-color: var(--color-accent);
            color: var(--color-accent);
        }

        .summary-totals-cards {
            display: flex;
            gap: var(--space-3);
            flex-wrap: wrap;
        }
        .summary-kpi-card {
            min-width: 120px;
            padding: 12px 16px;
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .summary-kpi-card .kpi-label {
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-muted-foreground);
            margin-bottom: 2px;
        }
        .summary-kpi-card .kpi-value {
            font-size: var(--text-xl);
            color: var(--color-foreground);
            font-weight: 800;
            display: block;
        }
        .summary-kpi-card .kpi-sub {
            display: block;
            font-size: 0.6875rem;
            color: var(--color-muted-foreground);
            margin-top: 2px;
        }

        /* ========== Status Banner ========== */
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

        /* ========== Schedule Summary Card & Table ========== */
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
            margin-bottom: var(--space-3);
        }
        .monthly-breakdown h4 {
            font-size: var(--text-base);
            font-weight: 700;
            color: var(--color-foreground);
            margin: 0;
        }
        .table-total-row {
            background: #F8FAFC;
            font-weight: 700;
        }
        .row-rate-pill {
            display: inline-block;
            padding: 1px 7px;
            border-radius: var(--radius-full);
            font-size: 0.72rem;
            font-family: var(--font-mono);
            font-weight: 700;
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        /* ========== Pre-Submission Checklist ========== */
        .form-ready-checklist {
            background: #F8FAFC;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            margin-top: var(--space-5);
        }
        .checklist-title {
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-muted-foreground);
            margin-bottom: 8px;
        }
        .checklist-items {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 18px;
        }
        .checklist-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--color-muted-foreground);
        }
        .checklist-item .chk-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #E2E8F0;
            color: #94A3B8;
            font-size: 0.625rem;
            font-weight: 800;
            font-style: normal;
        }
        .checklist-item.is-valid {
            color: var(--color-foreground);
        }
        .checklist-item.is-valid .chk-icon {
            background: #10B981;
            color: #FFFFFF;
        }

        /* ========== Actions Bar ========== */
        .form-actions-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-4);
            margin-top: var(--space-6);
            padding-top: var(--space-4);
            border-top: 1px solid var(--color-border);
        }
        .btn-kbd {
            display: inline-block;
            padding: 1px 6px;
            font-size: 0.6875rem;
            font-family: var(--font-mono);
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-xs);
            margin-left: 6px;
        }
        .issue-date-shortcuts {
            display: inline-flex;
            gap: 6px;
        }

        .spinner {
            width: 14px;
            height: 14px;
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
            .schedule-presets-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 720px) {
            .stepper-sticky-container { top: 56px; }
            .form-grid { grid-template-columns: minmax(0, 1fr); }
            .form-grid .span-2 { grid-column: auto; }
            .summary-toolbar { flex-direction: column; }
            .form-actions-bar { flex-direction: column-reverse; align-items: stretch; }
            .schedule-presets-grid { grid-template-columns: minmax(0, 1fr); }
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
            const rateSlider = $('attendance_rate_range');
            const hiddenInputs = $('attendance-inputs');

            const summary = $('schedule-summary');
            const summaryHelp = $('summary-help');
            const summaryError = $('summary-error');
            const statusEl = $('summary-status');
            const monthlyRows = $('monthly-breakdown-rows');

            // Calendar starts on Saturday (Bangladesh standard)
            const CAL_WEEKDAYS = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            const monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthNamesFull = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

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
                return `${monthNamesShort[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
            };

            const formatMonth = (year, month) => {
                return `${monthNamesFull[month - 1]} ${year}`;
            };

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
            // Holidays logic
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
                    ? '1 excluded'
                    : `${dates.length} excluded`;

                if (dates.length === 0) {
                    const empty = document.createElement('span');
                    empty.className = 'label-muted';
                    empty.style.fontStyle = 'italic';
                    empty.textContent = 'No extra holidays excluded.';
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

                $('cal-month-label').textContent = `${monthNamesFull[month]} ${year}`;
                const activeWeekdays = selectedDays().length;
                $('cal-sub').textContent = activeWeekdays > 0
                    ? `${activeWeekdays} class days/week · Friday is weekly govt off`
                    : 'Select class days to highlight them';

                const grid = $('cal-grid');
                grid.innerHTML = '';

                const first = new Date(year, month, 1);
                // Week starts on Saturday:
                // Sunday=0, Monday=1, ..., Friday=5, Saturday=6.
                // Saturday offset is 0, Sunday offset is 1, ..., Friday offset is 6.
                const offset = (first.getDay() + 1) % 7;
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
                    // Match with CAL_WEEKDAYS
                    const dayName = CAL_WEEKDAYS[(date.getDay() + 1) % 7];
                    const isFriday = (dayName === 'Friday');

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'cal-day';
                    button.textContent = String(day);
                    button.dataset.date = value;

                    if (isFriday) {
                        button.classList.add('is-friday-holiday');
                        button.title = `${value} · Friday (Government Holiday)`;
                    } else {
                        button.title = `${value} · ${dayName}`;
                    }

                    if (classDays.includes(dayName)) {
                        button.classList.add('is-class');
                    }

                    if (holidays.has(value)) {
                        button.classList.add('is-holiday');
                        button.setAttribute('aria-pressed', 'true');
                        button.title += ' (Excluded from class hours)';
                    }

                    if (value === today) {
                        button.classList.add('is-today');
                    }

                    button.addEventListener('click', () => toggleHoliday(value));
                    grid.appendChild(button);
                }
            };

            const shouldAutoCompensate = () => {
                const cb = $('auto-compensate-holidays');
                return Boolean(cb && cb.checked && courseSelect.value && startInput.value);
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
                if (shouldAutoCompensate()) {
                    runRefresh(true);
                } else {
                    scheduleRefresh();
                }
            };

            const syncCalendarToStart = () => {
                const start = parseDate(startInput.value);
                if (start) calCursor = new Date(start.getFullYear(), start.getMonth(), 1);
            };

            const syncCalendarToEnd = () => {
                const end = parseDate(endInput.value);
                if (end) calCursor = new Date(end.getFullYear(), end.getMonth(), 1);
            };

            // ------------------------------------------------------------------
            // Friday Notice check
            // ------------------------------------------------------------------

            const updateFridayWarning = () => {
                const fridayCb = $('day-cb-friday');
                const warningEl = $('friday-warning');
                if (fridayCb && warningEl) {
                    warningEl.style.display = fridayCb.checked ? 'flex' : 'none';
                }
            };

            // ------------------------------------------------------------------
            // Summary & 180-Hour Compliance rendering
            // ------------------------------------------------------------------

            const renderCompliance = (target, totalClass) => {
                const hoursPerDay = Number(hoursInput.value || 3);
                const totalSessions = hoursPerDay > 0 ? Math.round(totalClass / hoursPerDay) : 0;
                const targetSessions = (hoursPerDay > 0 && target > 0) ? Math.round(target / hoursPerDay) : 60;

                const counterEl = $('compliance-sessions-counter');
                if (counterEl) {
                    counterEl.textContent = `${totalSessions} / ${targetSessions} Sessions (${hoursPerDay}h/session)`;
                }

                const barEl = $('compliance-progress-bar');
                const badgeEl = $('compliance-status-badge');
                const pillEl = $('end-date-status-pill');

                if (barEl && badgeEl) {
                    if (target > 0) {
                        const percent = Math.min(100, Math.round((totalClass / target) * 100));
                        barEl.style.width = `${percent}%`;

                        if (totalClass === target && totalClass > 0) {
                            barEl.style.background = 'linear-gradient(90deg, #10B981, #059669)';
                            badgeEl.className = 'compliance-badge is-ok';
                            badgeEl.textContent = `✓ ${target}h Standard Met (Exact Match)`;
                            if (pillEl) {
                                pillEl.textContent = `✓ ${target}h Target Met`;
                                pillEl.className = 'auto-fit-pill is-ok';
                            }
                        } else if (totalClass < target) {
                            const diff = target - totalClass;
                            const diffSess = hoursPerDay > 0 ? Math.round(diff / hoursPerDay) : 0;
                            barEl.style.background = 'linear-gradient(90deg, #F59E0B, #D97706)';
                            badgeEl.className = 'compliance-badge is-warn';
                            badgeEl.textContent = `⚠️ ${totalClass}h / ${target}h (${diff}h Short · -${diffSess} Session${diffSess === 1 ? '' : 's'})`;
                            if (pillEl) {
                                pillEl.textContent = `${totalClass}h / ${target}h (-${diff}h)`;
                                pillEl.className = 'auto-fit-pill is-warn';
                            }
                        } else {
                            const diff = totalClass - target;
                            const diffSess = hoursPerDay > 0 ? Math.round(diff / hoursPerDay) : 0;
                            barEl.style.background = 'linear-gradient(90deg, #3B82F6, #6366F1)';
                            badgeEl.className = 'compliance-badge is-info';
                            badgeEl.textContent = `ℹ️ ${totalClass}h / ${target}h (+${diff}h Extra · +${diffSess} Session${diffSess === 1 ? '' : 's'})`;
                            if (pillEl) {
                                pillEl.textContent = `${totalClass}h / ${target}h (+${diff}h)`;
                                pillEl.className = 'auto-fit-pill is-warn';
                            }
                        }
                    } else {
                        barEl.style.width = '0%';
                        badgeEl.className = 'compliance-badge';
                        badgeEl.textContent = 'Select course curriculum';
                        if (pillEl) {
                            pillEl.textContent = '180h Target';
                            pillEl.className = 'auto-fit-pill';
                        }
                    }
                }
            };

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
                $('totals-class-sub').textContent = target > 0 ? `Curriculum: ${target} h` : 'No course target';
                $('totals-attendance').textContent = totalAttendance + ' h';
                $('totals-rate').textContent = rate + '%';

                $('foot-total-class').textContent = totalClass + ' h';
                $('foot-total-attendance').textContent = totalAttendance + ' h';
                $('foot-total-rate').textContent = rate + '%';

                $('calc-months').textContent = months > 0 ? `${months} month${months === 1 ? '' : 's'}` : '—';
                $('calc-current-hours').textContent = totalClass > 0 ? `${totalClass} h` : '—';
                $('calc-end-date').textContent = formatDate(endInput.value);
                $('calc-target-hours').textContent = target > 0 ? target + ' h' : '—';

                $('days-count-badge').textContent = `${classDays.length} day${classDays.length === 1 ? '' : 's'} selected`;

                renderCompliance(target, totalClass);

                if (months === 0) {
                    summary.hidden = true;
                    summaryHelp.hidden = false;
                    statusEl.hidden = true;
                    return;
                }

                summary.hidden = false;
                summaryHelp.hidden = true;

                $('summary-period').textContent = `${formatDate(startInput.value)} → ${formatDate(endInput.value)}`;
                $('summary-months').textContent = `${months} Month${months === 1 ? '' : 's'}`;
                $('summary-days-week').textContent = `${classDays.length} Days / Week`;
                $('summary-hours-day').textContent = `${hoursInput.value || 0} Hours / Day`;
                $('summary-class-days').textContent = classDays.map((day) => day.slice(0, 3)).join(', ');
                $('summary-row-count').textContent = `${months} Monthly Records`;

                monthlyRows.innerHTML = '';
                rows.forEach((row) => {
                    const rowRate = row.class_hours > 0
                        ? Math.round((row.attendance_hours / row.class_hours) * 100)
                        : 100;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${formatMonth(row.year, row.month)}</strong></td>
                        <td class="num">${row.class_hours} h</td>
                        <td class="num">${row.attendance_hours} h</td>
                        <td class="num"><span class="row-rate-pill">${rowRate}%</span></td>
                    `;
                    monthlyRows.appendChild(tr);
                });

                renderStatus(target, totalClass, months);
                updateChecklist();
            };

            const renderStatus = (target, totalClass, months) => {
                statusEl.hidden = false;
                statusEl.className = 'status-banner';
                statusEl.innerHTML = '';

                if (months > 24) {
                    statusEl.classList.add('is-danger');
                    const message = document.createElement('span');
                    message.innerHTML = '<strong>Schedule exceeded limit:</strong> A certificate supports at most 24 monthly records. Adjust the study start/end dates.';
                    statusEl.appendChild(message);
                    return;
                }

                if (target <= 0) {
                    statusEl.classList.add('is-info');
                    const message = document.createElement('span');
                    message.textContent = 'Select a course curriculum to verify total scheduled class hours.';
                    statusEl.appendChild(message);
                    return;
                }

                const difference = totalClass - target;

                if (difference === 0) {
                    statusEl.classList.add('is-ok');
                    const message = document.createElement('span');
                    message.innerHTML = `<strong>Curriculum Exact Match (${totalClass} h):</strong> Scheduled hours match course requirements perfectly. Friday government holidays and exclusions have been respected.`;
                    statusEl.appendChild(message);
                    return;
                }

                statusEl.classList.add('is-warn');
                const message = document.createElement('span');
                const hint = difference < 0
                    ? `short by ${Math.abs(difference)} h`
                    : `exceeds by ${difference} h`;
                message.innerHTML = `<strong>Discrepancy: ${totalClass} h scheduled of ${target} h required.</strong> Schedule is ${hint}. Click below to fit the study end date.`;
                statusEl.appendChild(message);

                const fit = document.createElement('button');
                fit.type = 'button';
                fit.className = 'btn btn-secondary btn-sm';
                fit.textContent = `Auto-Fit End Date to ${target} h`;
                fit.addEventListener('click', () => runRefresh(true));
                statusEl.appendChild(fit);
            };

            const renderAll = () => {
                renderHiddenInputs();
                renderSummary();
            };

            const applyRate = (rateVal) => {
                const rate = Math.max(0, Math.min(100, parseInt(rateVal, 10) || 100));
                rateInput.value = rate;
                rateSlider.value = rate;
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
                    showError('Please enter the study start date first.');
                    return;
                }
                if (!classDays.length) {
                    showError('Select at least one class day per week.');
                    return;
                }
                if (!hours) {
                    showError('Enter the scheduled class hours per day.');
                    return;
                }

                await withBusy(button, 'Calculating…', async () => {
                    try {
                        let end = endInput.value;

                        if (recalculateEnd) {
                            const target = targetHours();
                            if (!target) {
                                throw new Error('Please select a course so the target hours can be verified.');
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
                        showError(error.message || 'Could not calculate the attendance schedule.');
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
            // Date & Age helpers
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
                const pill = $('student-age-pill');
                if (!input || !target) return;

                if (!input.value) {
                    target.textContent = 'Format: YYYY-MM-DD. Age is calculated automatically.';
                    if (pill) pill.style.display = 'none';
                    return;
                }

                const birth = parseDate(input.value);
                if (!birth || Number.isNaN(birth.getTime())) {
                    target.textContent = 'Please enter a valid date.';
                    if (pill) pill.style.display = 'none';
                    return;
                }

                const today = new Date();
                let age = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                    age--;
                }

                if (age >= 0) {
                    target.textContent = `Calculated Age: ${age} years old`;
                    if (pill) {
                        pill.textContent = `🎂 ${age} years old`;
                        pill.style.display = 'inline-flex';
                    }
                }
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
                $('course-code').textContent = option.dataset.level || 'JLPT';
                $('course-name').textContent = option.dataset.name || '—';
                $('course-aim').textContent = option.dataset.aim || '—';
                $('course-book').textContent = option.dataset.book || '—';
                $('course-total').textContent = option.dataset.totalhours ? `${option.dataset.totalhours} Hours` : '—';
                $('course-default').textContent = option.dataset.hours && option.dataset.days
                    ? `${option.dataset.hours}h / day · ${option.dataset.days} days / week`
                    : '—';

                const target = targetHours();
                $('calc-target-pill').textContent = target > 0 ? `${target} h curriculum` : 'No target hours';
                $('calc-target-hours').textContent = target > 0 ? `${target} h` : '—';
                $('calculated-hours-display').hidden = target <= 0;
                $('calc-help').hidden = target > 0;
            };

            const applyCourseDefaults = () => {
                const option = courseSelect.selectedOptions[0];
                if (!option) return;

                const hours = option.dataset.hours;
                const days = option.dataset.days;

                if (hours) hoursInput.value = hours;
                if (days) {
                    daysInput.value = days;
                    syncCheckboxes(parseInt(days, 10));
                }

                renderCalendar();
                runRefresh(true);
            };

            // ------------------------------------------------------------------
            // Day helpers
            // ------------------------------------------------------------------

            const syncDaysPerWeek = () => {
                const checked = form.querySelectorAll('input[name="class_days[]"]:checked').length;
                if (checked > 0) daysInput.value = checked;
                $('days-count-badge').textContent = `${checked} day${checked === 1 ? '' : 's'} selected`;
                updateFridayWarning();
            };

            const syncCheckboxes = (count) => {
                const checkboxes = [...form.querySelectorAll('input[name="class_days[]"]')];
                checkboxes.forEach((checkbox, index) => {
                    checkbox.checked = index < count;
                });
                syncDaysPerWeek();
            };

            const markPreset = (activeButton) => {
                document.querySelectorAll('.preset-card-btn').forEach((btn) => btn.classList.remove('active'));
                if (activeButton) activeButton.classList.add('active');
            };

            // ------------------------------------------------------------------
            // Pre-submission Checklist & Stepper Checkmarks
            // ------------------------------------------------------------------

            const updateChecklist = () => {
                const hasStudent = Boolean($('student_name_en')?.value && $('student_date_of_birth')?.value && $('student_gender')?.value);
                const hasCourse = Boolean(courseSelect?.value);
                const hasSchedule = Boolean(startInput?.value && endInput?.value && selectedDays().length > 0);
                const target = targetHours();
                const totalClass = rows.reduce((sum, row) => sum + row.class_hours, 0);
                const hasHours = target > 0 && totalClass === target;
                const hasSign = Boolean($('instructor_1')?.value);

                const setChk = (id, valid) => {
                    const el = $(id);
                    if (el) el.classList.toggle('is-valid', valid);
                };

                setChk('chk-item-student', hasStudent);
                setChk('chk-item-course', hasCourse);
                setChk('chk-item-schedule', hasSchedule);
                setChk('chk-item-hours', hasHours);
                setChk('chk-item-sign', hasSign);

                // Update Stepper item checkmarks
                $('step-1-chk')?.parentElement.classList.toggle('is-completed', hasStudent);
                $('step-2-chk')?.parentElement.classList.toggle('is-completed', hasCourse);
                $('step-3-chk')?.parentElement.classList.toggle('is-completed', hasSchedule);
                $('step-4-chk')?.parentElement.classList.toggle('is-completed', rows.length > 0);
                $('step-5-chk')?.parentElement.classList.toggle('is-completed', hasSign);

                // Update Progress bar percentage
                let score = 0;
                if (hasStudent) score += 20;
                if (hasCourse) score += 20;
                if (hasSchedule) score += 20;
                if (rows.length > 0) score += 20;
                if (hasSign) score += 20;
                $('stepper-progress-fill').style.width = `${Math.max(10, score)}%`;
            };

            // ------------------------------------------------------------------
            // Event Listeners
            // ------------------------------------------------------------------

            rateInput.addEventListener('change', () => applyRate(rateInput.value));
            rateInput.addEventListener('blur', () => applyRate(rateInput.value));
            rateSlider.addEventListener('input', () => applyRate(rateSlider.value));

            document.querySelectorAll('.rate-chip').forEach((chip) => {
                chip.addEventListener('click', () => {
                    applyRate(chip.dataset.rate);
                });
            });

            startInput.addEventListener('change', () => {
                syncCalendarToStart();
                renderCalendar();
                scheduleRefresh();
                updateChecklist();
            });

            endInput.addEventListener('change', () => {
                syncIssuedOn();
                renderCalendar();
                scheduleRefresh();
                updateChecklist();
            });

            const syncActivePresetWithSelection = () => {
                const days = selectedDays().join(',');
                const hours = String(hoursInput.value || '');
                document.querySelectorAll('.preset-card-btn').forEach((btn) => {
                    btn.classList.toggle('active', btn.dataset.days === days && btn.dataset.hours === hours);
                });
            };

            hoursInput.addEventListener('change', () => {
                syncActivePresetWithSelection();
                renderSummary();
                scheduleRefresh();
                updateChecklist();
            });

            daysInput.addEventListener('change', () => {
                const value = parseInt(daysInput.value, 10);
                if (!Number.isNaN(value) && value >= 1 && value <= 7) {
                    syncCheckboxes(value);
                    syncActivePresetWithSelection();
                    renderCalendar();
                    scheduleRefresh();
                    updateChecklist();
                }
            });

            form.querySelectorAll('input[name="class_days[]"]').forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    syncDaysPerWeek();
                    syncActivePresetWithSelection();
                    renderCalendar();
                    scheduleRefresh();
                    updateChecklist();
                });
            });

            document.querySelectorAll('.preset-card-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    const days = (button.dataset.days || '').split(',').filter(Boolean);
                    const hours = button.dataset.hours;

                    if (hours) hoursInput.value = hours;
                    form.querySelectorAll('input[name="class_days[]"]').forEach((checkbox) => {
                        checkbox.checked = days.includes(checkbox.value);
                    });

                    syncDaysPerWeek();
                    syncActivePresetWithSelection();
                    renderCalendar();

                    if (!startInput.value) {
                        const now = new Date();
                        startInput.value = isoOf(new Date(now.getFullYear(), now.getMonth(), 1));
                        syncCalendarToStart();
                    }

                    if (courseSelect.value) {
                        runRefresh(true);
                    } else {
                        scheduleRefresh();
                    }
                    updateChecklist();
                });
            });

            // Start Date Quick Chips (1st of month, Next month, Today)
            document.querySelectorAll('.start-chip').forEach((chip) => {
                chip.addEventListener('click', () => {
                    const type = chip.dataset.startType;
                    const now = new Date();
                    let targetDate;

                    if (type === 'this-month-1st') {
                        targetDate = new Date(now.getFullYear(), now.getMonth(), 1);
                    } else if (type === 'next-month-1st') {
                        targetDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
                    } else if (type === 'today') {
                        targetDate = now;
                    }

                    if (targetDate) {
                        startInput.value = isoOf(targetDate);
                        syncCalendarToStart();
                        renderCalendar();
                        if (courseSelect.value) {
                            runRefresh(true);
                        } else {
                            scheduleRefresh();
                        }
                        updateChecklist();
                    }
                });
            });

            // Session Nudge (+1 Class / -1 Class)
            const nudgeSession = (direction) => {
                const classDays = selectedDays();
                if (!classDays.length) {
                    showError('Select at least one class day first.');
                    return;
                }

                let currentEnd = parseDate(endInput.value);
                if (!currentEnd || Number.isNaN(currentEnd.getTime())) {
                    if (startInput.value && courseSelect.value) {
                        runRefresh(true);
                    }
                    return;
                }

                const stepDays = direction > 0 ? 1 : -1;
                let candidate = new Date(currentEnd.getTime());
                let found = false;

                // Look ahead or behind up to 30 days to find the next/prev active class day
                for (let i = 0; i < 30; i++) {
                    candidate.setDate(candidate.getDate() + stepDays);
                    const iso = isoOf(candidate);
                    const dayName = CAL_WEEKDAYS[(candidate.getDay() + 1) % 7];

                    if (classDays.includes(dayName) && !holidays.has(iso)) {
                        endInput.value = iso;
                        found = true;
                        break;
                    }
                }

                if (found) {
                    syncCalendarToEnd();
                    renderCalendar();
                    syncIssuedOn();
                    runRefresh(false);
                    updateChecklist();
                }
            };

            document.querySelectorAll('.session-nudge').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const nudge = parseInt(btn.dataset.nudge, 10);
                    nudgeSession(nudge);
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
                syncActivePresetWithSelection();
                renderCalendar();

                if (courseSelect.value) {
                    runRefresh(!isEdit);
                }
                updateChecklist();
            });

            $('apply-course-defaults-btn')?.addEventListener('click', applyCourseDefaults);

            holidayInput.addEventListener('input', () => {
                holidays = parseHolidayText(holidayInput.value);
                renderCalendar();
                renderHolidayChips();
                if (shouldAutoCompensate()) {
                    runRefresh(true);
                } else {
                    scheduleRefresh();
                }
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
                if (shouldAutoCompensate()) {
                    runRefresh(true);
                } else {
                    scheduleRefresh();
                }
            });

            // Quick add Bangladesh national holidays for active study period years
            document.querySelectorAll('.quick-add-chip').forEach((chip) => {
                chip.addEventListener('click', () => {
                    const mmdd = chip.dataset.bdHoliday;
                    const years = studyYears();
                    years.forEach((yr) => {
                        holidays.add(`${yr}-${mmdd}`);
                    });
                    writeHolidays();
                    renderCalendar();
                    renderHolidayChips();
                    if (shouldAutoCompensate()) {
                        runRefresh(true);
                    } else {
                        scheduleRefresh();
                    }
                });
            });

            $('cal-prev').addEventListener('click', () => {
                calCursor = new Date(calCursor.getFullYear(), calCursor.getMonth() - 1, 1);
                renderCalendar();
            });

            $('cal-next').addEventListener('click', () => {
                calCursor = new Date(calCursor.getFullYear(), calCursor.getMonth() + 1, 1);
                renderCalendar();
            });

            $('jump-to-start-btn')?.addEventListener('click', () => {
                syncCalendarToStart();
                renderCalendar();
            });

            $('jump-to-end-btn')?.addEventListener('click', () => {
                syncCalendarToEnd();
                renderCalendar();
            });

            $('calc-end-date-btn').addEventListener('click', () => runRefresh(true));
            $('refresh-summary').addEventListener('click', () => runRefresh(false));

            $('student_date_of_birth').addEventListener('input', () => {
                updateStudentAge();
                updateChecklist();
            });

            $('student_name_en')?.addEventListener('blur', (e) => {
                // Auto-capitalize words
                const val = e.target.value.trim();
                if (val) {
                    e.target.value = val.replace(/\b\w/g, (char) => char.toUpperCase());
                }
                updateChecklist();
            });

            $('student_gender')?.addEventListener('change', updateChecklist);
            $('instructor_1')?.addEventListener('change', updateChecklist);

            issuedInput.addEventListener('input', () => {
                issuedDirty = true;
            });

            $('issue-match-end')?.addEventListener('click', () => {
                if (endInput.value) {
                    issuedInput.value = endInput.value;
                    issuedDirty = true;
                }
            });

            $('issue-today')?.addEventListener('click', () => {
                const today = isoOf(new Date());
                const end = endInput.value;
                issuedInput.value = (end && today < end) ? end : today;
                issuedDirty = true;
            });

            // Smooth Step Navigators
            document.querySelectorAll('.next-step-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const targetId = btn.dataset.next;
                    const el = $(targetId);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            document.querySelectorAll('.prev-step-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const targetId = btn.dataset.prev;
                    const el = $(targetId);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            // Scrollspy for Stepper
            const stepCards = document.querySelectorAll('.form-section-card');
            const stepNavLinks = document.querySelectorAll('.form-step');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const id = entry.target.id;
                        stepNavLinks.forEach((link) => {
                            link.classList.toggle('active', link.getAttribute('href') === `#${id}`);
                        });
                    }
                });
            }, { rootMargin: '-20% 0px -60% 0px' });

            stepCards.forEach((card) => observer.observe(card));

            // Keyboard shortcut: Ctrl+S / Cmd+S
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
                    e.preventDefault();
                    form.requestSubmit();
                }
            });

            form.addEventListener('submit', (event) => {
                if (rows.length === 0) {
                    event.preventDefault();
                    showError('Please generate the schedule breakdown before saving. Pick a course and valid study dates.');
                    document.getElementById('step-4').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            // ------------------------------------------------------------------
            // Initialise
            // ------------------------------------------------------------------

            rateInput.value = initialRate;
            rateSlider.value = initialRate;
            if (!startInput.value && !isEdit) {
                const now = new Date();
                startInput.value = isoOf(new Date(now.getFullYear(), now.getMonth(), 1));
            }
            holidays = parseHolidayText(holidayInput.value);
            syncCalendarToStart();
            syncIssuedOn();
            updateStudentAge();
            updateCourseInfo();
            updateFridayWarning();
            syncActivePresetWithSelection();
            renderCalendar();
            renderHolidayChips();
            renderAll();
            updateChecklist();

            if (rows.length === 0 && courseSelect.value && startInput.value && endInput.value && selectedDays().length) {
                runRefresh(false);
            }
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\2026 New Project\Eminent Overseas Certificate\resources\views/admin/certificates/form.blade.php ENDPATH**/ ?>