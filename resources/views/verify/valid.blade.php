@extends('verify.layout')

@section('title', 'Certificate Valid')

@section('content')
    <div class="banner valid">
        <div class="icon-wrapper">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <h1>Official Certificate Verified</h1>
            <p>This credential has been authenticated as genuine and issued by {{ $school['name'] }}.</p>
        </div>
    </div>
    <div class="content">
        <dl>
            <dt>Certificate No.</dt>
            <dd><span class="mono-val">{{ $certificate->certificate_no }}</span></dd>

            <dt>Student Name</dt>
            <dd>
                <span style="font-size: 16px; font-weight: 700;">{{ $certificate->student_name_en }}</span>
                @if ($certificate->student_name_ja)
                    <span class="ja-tag">{{ $certificate->student_name_ja }}</span>
                @endif
            </dd>

            <dt>Course Program</dt>
            <dd>
                <div>{{ $certificate->course->name_ja }} / {{ $certificate->course->name_en }}</div>
                <span style="display: inline-block; font-size: 12px; font-weight: 700; color: #2563EB; background: #EFF6FF; padding: 1px 7px; border-radius: 4px; margin-top: 3px;">Level: {{ $certificate->course->level_code }}</span>
            </dd>

            <dt>Study Period</dt>
            <dd>{{ $certificate->study_start->format('Y-m-d') }} &rarr; {{ $certificate->study_end->format('Y-m-d') }}</dd>

            <dt>Curriculum Hours</dt>
            <dd>{{ $certificate->total_class_hours }} hours <span style="color: #059669; font-weight: 700;">({{ round((float) $certificate->attendance_rate) }}% attendance)</span></dd>

            <dt>Issued On</dt>
            <dd>{{ $certificate->issued_on->format('Y-m-d') }}</dd>

            <dt>Issuing Authority</dt>
            <dd>{{ $school['name'] }}</dd>

            <dt>Validation Status</dt>
            <dd><span style="color: #059669; background: #ECFDF5; padding: 2px 10px; border-radius: 9999px; font-size: 13px; font-weight: 700; border: 1px solid #A7F3D0; display: inline-flex; align-items: center; gap: 5px;">&bull; Issued & Genuine</span></dd>
        </dl>

        <div class="official-seal-note">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>
            <span>This verification confirms the authentic record on file at {{ $school['name'] }}. Protected by cryptographic ULID verification codes.</span>
        </div>

        <a class="btn" href="{{ route('verify.form') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <span>Verify Another Certificate</span>
        </a>
    </div>
@endsection
