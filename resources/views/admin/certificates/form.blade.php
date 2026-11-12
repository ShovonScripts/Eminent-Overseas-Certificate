@extends('layouts.admin')

@php
    $isEdit = $certificate->exists;
    $selectedDays = old('class_days', $certificate->class_days ?? []);
    $selectedDays = is_array($selectedDays) ? $selectedDays : [];
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
@endphp

@section('title', $isEdit ? 'Edit draft certificate' : 'New certificate')
@section('eyebrow', 'Certificates')

@section('topbar_actions')
    @if ($isEdit)
        <a class="btn btn-secondary" href="{{ route('certificates.preview', $certificate) }}" target="_blank" rel="noopener">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            <span>Preview PDF</span>
        </a>
    @endif
    <a class="btn btn-ghost" href="{{ route('admin.certificates.index') }}">&larr; Back to list</a>
@endsection

@section('content')
    <form id="certificate-form" method="post"
          action="{{ $isEdit ? route('admin.certificates.update', $certificate) : route('admin.certificates.store') }}">
        @csrf
        @if ($isEdit)
            @method('put')
        @endif

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Student Personal Information</h2>
                    <p>Enter student details. A master student record will be linked or generated automatically.</p>
                </div>
                @if ($isEdit)
                    <span class="badge badge-draft">Draft #{{ $certificate->id }}</span>
                @endif
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="student_name_en">English name</label>
                        <input id="student_name_en" class="input" name="student_name_en" type="text" required
                               value="{{ old('student_name_en', $certificate->student_name_en ?? '') }}">
                        @error('student_name_en')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="student_name_ja">Japanese name <span class="text-muted">(optional)</span></label>
                        <input id="student_name_ja" class="input" name="student_name_ja" type="text"
                               value="{{ old('student_name_ja', $certificate->student_name_ja ?? '') }}">
                        @error('student_name_ja')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="student_date_of_birth">Date of birth</label>
                        <input id="student_date_of_birth" class="input" name="student_date_of_birth" type="date" required
                               value="{{ old('student_date_of_birth', $certificate->student_date_of_birth?->format('Y-m-d') ?? '') }}">
                        @error('student_date_of_birth')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="student_gender">Sex</label>
                        <select id="student_gender" class="select" name="student_gender" required>
                            <option value="">Select…</option>
                            @foreach (\App\Enums\Gender::cases() as $gender)
                                <option value="{{ $gender->value }}" @selected(old('student_gender', $certificate->student_gender?->value) === $gender->value)>
                                    {{ $gender->label() }} / {{ $gender->japaneseLabel() }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_gender')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="student_email">Email <span class="text-muted">(optional)</span></label>
                        <input id="student_email" class="input" name="student_email" type="email"
                               value="{{ old('student_email', $certificate->student_email ?? '') }}">
                        @error('student_email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="student_phone">Phone <span class="text-muted">(optional)</span></label>
                        <input id="student_phone" class="input" name="student_phone" type="text"
                               value="{{ old('student_phone', $certificate->student_phone ?? '') }}">
                        @error('student_phone')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Course Curriculum</h2>
                    <p>Select the recognized Japanese curriculum for this completion certificate.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="course_id">Course</label>
                        <select id="course_id" class="select" name="course_id" required>
                            <option value="">Select a course…</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}"
                                        data-hours="{{ $course->default_hours_per_day }}"
                                        data-days="{{ $course->default_days_per_week }}"
                                        @selected((string) old('course_id', $certificate->course_id) === (string) $course->id)>
                                    {{ $course->name_en }} ({{ $course->level_code }}) — {{ $course->total_hours }}h
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Study Period & Schedule</h2>
                    <p>Class dates, duration, and scheduled days as they will appear on the physical certificate.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="study_start">Study start</label>
                        <input id="study_start" class="input" name="study_start" type="date" required
                               value="{{ old('study_start', $certificate->study_start?->format('Y-m-d')) }}">
                        @error('study_start')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="study_end">Study end</label>
                        <input id="study_end" class="input" name="study_end" type="date" required
                               value="{{ old('study_end', $certificate->study_end?->format('Y-m-d')) }}">
                        @error('study_end')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="hours_per_day">Hours per day</label>
                        <input id="hours_per_day" class="input" name="hours_per_day" type="number" min="1" max="12" required
                               value="{{ old('hours_per_day', $certificate->hours_per_day ?? 3) }}">
                        @error('hours_per_day')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="days_per_week">Days per week</label>
                        <input id="days_per_week" class="input" name="days_per_week" type="number" min="1" max="7" required
                               value="{{ old('days_per_week', $certificate->days_per_week ?? 3) }}">
                        @error('days_per_week')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field span-2">
                        <span class="label">Class days</span>
                        <div class="check-grid">
                            @foreach (\App\Http\Requests\CertificateRequest::WEEKDAYS as $day)
                                <label class="check">
                                    <input type="checkbox" name="class_days[]" value="{{ $day }}"
                                           @checked(in_array($day, $selectedDays, true))>
                                    {{ $day }}
                                </label>
                            @endforeach
                        </div>
                        @error('class_days')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field span-2">
                        <label for="national_holidays">Other holidays <span class="text-muted">(optional)</span></label>
                        <textarea id="national_holidays" class="textarea" name="national_holidays"
                                  placeholder="6月9日; 6月11日; 8月16日; 10月1日&#10;or one Y-m-d date per line">{{ old('national_holidays', $certificate->national_holidays) }}</textarea>
                        <div class="help">Separate dates with semicolons, commas or new lines. Both Japanese dates and Y-m-d work.</div>
                        @error('national_holidays')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Monthly Attendance Breakdown</h2>
                    <p>Add attendance records per month. Attendance hours cannot exceed class hours.</p>
                </div>
                <button class="btn btn-gold btn-sm" type="button" id="suggest-hours">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span id="suggest-hours-label">Suggest class hours</span>
                </button>
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
                    @foreach ($attendanceRows as $index => $row)
                        <tr class="attendance-row">
                            <td>
                                <input class="input" type="number" name="attendances[{{ $index }}][year]" min="2000" max="2100" required
                                       value="{{ $row['year'] ?? now()->year }}">
                            </td>
                            <td>
                                <input class="input" type="number" name="attendances[{{ $index }}][month]" min="1" max="12" required
                                       value="{{ $row['month'] ?? 1 }}">
                            </td>
                            <td class="num">
                                <input class="input" type="number" name="attendances[{{ $index }}][class_hours]" min="0" max="2000" required
                                       value="{{ $row['class_hours'] ?? 0 }}">
                            </td>
                            <td class="num">
                                <input class="input" type="number" name="attendances[{{ $index }}][attendance_hours]" min="0" max="2000" required
                                       value="{{ $row['attendance_hours'] ?? 0 }}">
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
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-body" style="padding-top: var(--space-4);">
                <button class="btn btn-secondary btn-sm" type="button" id="add-attendance-row">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add month</span>
                </button>
                @error('attendances')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Signatures & Issue Date</h2>
                    <p>Assign authorizing instructors. The primary instructor prints on the left signature line.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="instructor_1">Left instructor</label>
                        <select id="instructor_1" class="select" name="instructors[]" required>
                            <option value="">Select an instructor…</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}" @selected((string) $selectedInstructors[0] === (string) $instructor->id)>
                                    {{ $instructor->name }} — {{ $instructor->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="instructor_2">Right instructor <span class="text-muted">(optional)</span></label>
                        <select id="instructor_2" class="select" name="instructors[]">
                            <option value="">No second instructor</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}" @selected((string) $selectedInstructors[1] === (string) $instructor->id)>
                                    {{ $instructor->name }} — {{ $instructor->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="issued_on">Date of issue</label>
                        <input id="issued_on" class="input" name="issued_on" type="date" required
                               value="{{ old('issued_on', $certificate->issued_on?->format('Y-m-d')) }}">
                        <div class="help">Must be on or after the study end date.</div>
                        @error('issued_on')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="{{ $isEdit ? route('admin.certificates.show', $certificate) : route('admin.certificates.index') }}">Cancel</a>
                <button class="btn btn-primary" type="submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>{{ $isEdit ? 'Save draft' : 'Create draft' }}</span>
                </button>
            </div>
        </div>
    </form>

    <template id="attendance-template">
        <tr class="attendance-row">
            <td><input class="input" type="number" name="attendances[__INDEX__][year]" min="2000" max="2100" required value="{{ now()->year }}"></td>
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
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('certificate-form');
            const rows = document.getElementById('attendance-rows');
            const template = document.getElementById('attendance-template');
            const suggestButton = document.getElementById('suggest-hours');
            const suggestLabel = document.getElementById('suggest-hours-label') || suggestButton;
            const suggestError = document.getElementById('suggest-error');
            let nextIndex = {{ count($attendanceRows) }};

            const refreshRates = () => {
                rows.querySelectorAll('.attendance-row').forEach((row) => {
                    const classHours = Number(row.querySelector('[name$="[class_hours]"]')?.value || 0);
                    const attendance = Number(row.querySelector('[name$="[attendance_hours]"]')?.value || 0);
                    const rate = classHours > 0 ? Math.round(attendance / classHours * 100) : 0;
                    row.querySelector('[data-rate]').textContent = rate + '%';
                });
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

            rows.addEventListener('input', (event) => {
                if (event.target.matches('input[type="number"]')) {
                    refreshRates();
                }
            });

            rows.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');
                if (!button) return;
                if (rows.querySelectorAll('.attendance-row').length > 1) {
                    button.closest('.attendance-row').remove();
                    refreshRates();
                }
            });

            document.getElementById('add-attendance-row').addEventListener('click', () => {
                addRow().querySelector('input').focus();
            });

            document.getElementById('course_id').addEventListener('change', (event) => {
                const option = event.target.selectedOptions[0];
                const hours = option?.dataset.hours;
                const days = option?.dataset.days;

                if (hours) document.getElementById('hours_per_day').value = hours;
                if (days) document.getElementById('days_per_week').value = days;
            });

            form.querySelectorAll('input[name="class_days[]"]').forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    const checked = form.querySelectorAll('input[name="class_days[]"]:checked').length;

                    if (checked > 0) {
                        document.getElementById('days_per_week').value = checked;
                    }
                });
            });

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
                    const response = await fetch('{{ route('admin.certificates.suggest') }}', {
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

                    refreshRates();
                } catch (error) {
                    suggestError.textContent = error.message;
                    suggestError.style.display = 'flex';
                } finally {
                    suggestButton.disabled = false;
                    suggestLabel.textContent = 'Suggest class hours';
                }
            });

            refreshRates();
        })();
    </script>
@endpush
