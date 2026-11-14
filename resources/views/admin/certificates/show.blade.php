@extends('layouts.admin')

@section('title', $certificate->certificate_no ?? 'Draft certificate')
@section('eyebrow', 'Certificate Details')

@section('topbar_actions')
    @if ($certificate->isDraft())
        <a class="btn btn-secondary" href="{{ route('admin.certificates.edit', $certificate) }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
            </svg>
            <span>Edit Draft</span>
        </a>
    @endif
    <a class="btn btn-secondary" href="{{ route('certificates.preview', $certificate) }}" target="_blank" rel="noopener">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span>Preview PDF</span>
    </a>
    @if (! $certificate->isDraft())
        <a class="btn btn-primary" href="{{ route('certificates.pdf', $certificate) }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            <span>Download PDF</span>
        </a>
    @endif
    <a class="btn btn-ghost" href="{{ route('admin.certificates.index') }}">
        &larr; Back to list
    </a>
@endsection

@section('content')
    @if ($certificate->isDraft())
        <div class="alert alert-warning">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <strong>Draft Certificate Notice:</strong> This certificate is currently a draft. It has no assigned certificate number, QR code or public verification page until it is formally issued.
            </div>
        </div>
    @elseif ($certificate->isRevoked())
        <div class="alert alert-danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <div>
                <strong>Certificate Revoked:</strong> This certificate was revoked on {{ $certificate->revoked_at?->format('Y-m-d H:i') }}.
                @if ($certificate->revoke_reason)
                    <div class="mt-1">Reason: <em>{{ $certificate->revoke_reason }}</em></div>
                @endif
            </div>
        </div>
    @endif

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Student & Course Summary</h2>
                    <p>{{ $certificate->course?->name_en }} &middot; <span class="badge badge-muted" style="font-size: 0.65rem;">{{ $certificate->course?->level_code }}</span></p>
                </div>
                <span class="badge badge-{{ $certificate->status->value }}">{{ $certificate->status->label() }}</span>
            </div>
            <div class="card-body">
                <dl class="detail-grid">
                    <dt>Certificate No.</dt>
                    <dd>
                        @if ($certificate->certificate_no)
                            <span class="mono" style="font-size: 1rem; color: #1E3A8A; font-weight: 700;">{{ $certificate->certificate_no }}</span>
                        @else
                            <span class="text-muted">Not assigned yet (Draft)</span>
                        @endif
                    </dd>

                    <dt>Student</dt>
                    <dd>
                        <strong style="font-size: 1rem; color: #0F172A;">{{ $certificate->student_name_en }}</strong>
                        @if ($certificate->student_name_ja)
                            <div class="text-muted" style="font-size: 0.85rem;">{{ $certificate->student_name_ja }}</div>
                        @endif
                        <div class="text-muted mt-1" style="font-size: 0.75rem; line-height: 1.4;">
                            DOB: <strong>{{ $certificate->student_date_of_birth?->format('Y-m-d') }}</strong> | 
                            Sex: <strong>{{ $certificate->student_gender?->label() }} / {{ $certificate->student_gender?->japaneseLabel() }}</strong>
                            @if ($certificate->student_email)
                                <br>Email: {{ $certificate->student_email }}
                            @endif
                            @if ($certificate->student_phone)
                                | Phone: {{ $certificate->student_phone }}
                            @endif
                        </div>
                    </dd>

                    <dt>Study Period</dt>
                    <dd>
                        {{ $certificate->study_start?->format('Y-m-d') }} &rarr; {{ $certificate->study_end?->format('Y-m-d') }}
                    </dd>

                    <dt>Schedule</dt>
                    <dd>{{ $certificate->hours_per_day }} hours/day, {{ $certificate->days_per_week }} days/week</dd>

                    <dt>Class Days</dt>
                    <dd>{{ implode(', ', $certificate->class_days ?? []) }}</dd>

                    <dt>Other Holidays</dt>
                    <dd>{{ $certificate->national_holidays ?: '—' }}</dd>

                    <dt>Issue Date</dt>
                    <dd>{{ $certificate->issued_on?->format('Y-m-d') ?? '—' }}</dd>

                    @if ($certificate->issued_at)
                        <dt>Issued At</dt>
                        <dd>
                            {{ $certificate->issued_at->format('Y-m-d H:i') }}
                            @if ($certificate->issuedBy)
                                <span class="text-muted">by {{ $certificate->issuedBy->name }}</span>
                            @endif
                        </dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Public Verification Record</h2>
                    <p>Cryptographic QR identity parameters</p>
                </div>
            </div>
            <div class="card-body">
                @if ($certificate->verification_code)
                    <dl class="detail-grid">
                        <dt>Verification Code</dt>
                        <dd><span class="mono" style="font-weight: 700; color: #0F172A;">{{ $certificate->verification_code }}</span></dd>

                        <dt>Public URL</dt>
                        <dd>
                            <a href="{{ $certificate->verifyUrl() }}" target="_blank" rel="noopener" style="font-size: 0.8125rem; word-break: break-all; display: inline-flex; align-items: center; gap: 4px;">
                                <span>{{ $certificate->verifyUrl() }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                        </dd>

                        <dt>PDF SHA-256</dt>
                        <dd class="mono" style="font-size: 0.75rem; word-break: break-all; color: #64748B;">{{ $certificate->pdf_sha256 ?: '—' }}</dd>
                    </dl>
                @else
                    <div style="background: #F8FAFC; padding: var(--space-5); border-radius: var(--radius-md); border: 1px dashed var(--color-border); text-align: center;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 8px;">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <rect width="5" height="5" x="7" y="7"/>
                            <rect width="5" height="5" x="7" y="12"/>
                            <rect width="5" height="5" x="12" y="7"/>
                        </svg>
                        <p class="text-muted mb-0" style="font-size: 0.8125rem;">The verification code, QR code and public URL will be generated automatically when this certificate is issued.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h2>Monthly Attendance Hours</h2>
                <p>{{ $certificate->total_class_hours }} total class hours &middot; {{ $certificate->total_attendance_hours }} attended &middot; <strong style="color: #059669;">{{ round((float) $certificate->attendance_rate) }}%</strong></p>
            </div>
        </div>
        @if ($certificate->attendances->isEmpty())
            <div class="empty">
                <strong>No attendance breakdown recorded</strong>
                <p class="text-muted">Monthly attendance hours should be configured before issuing the certificate.</p>
            </div>
        @else
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Month Period</th>
                        <th class="num">Class Hours</th>
                        <th class="num">Attended Hours</th>
                        <th class="num">Attendance Rate</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($certificate->attendances as $attendance)
                        <tr>
                            <td><strong style="color: #0F172A;">{{ $attendance->year }}-{{ str_pad((string) $attendance->month, 2, '0', STR_PAD_LEFT) }}</strong></td>
                            <td class="num">{{ $attendance->class_hours }} hrs</td>
                            <td class="num">{{ $attendance->attendance_hours }} hrs</td>
                            <td class="num">
                                <span class="badge {{ $attendance->attendanceRate() >= 80 ? 'badge-issued' : 'badge-warning' }}">
                                    {{ $attendance->attendanceRate() }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Instructor Signatures</h2>
                    <p>Printed on the bottom border of the certificate</p>
                </div>
            </div>
            <div class="card-body">
                @if ($certificate->instructors->isEmpty())
                    <p class="text-muted mb-0">No instructors selected.</p>
                @else
                    <dl class="detail-grid">
                        @foreach ($certificate->instructors as $instructor)
                            <dt>{{ $loop->first ? 'Primary Instructor' : 'Secondary Instructor' }}</dt>
                            <dd>
                                <strong>{{ $instructor->name }}</strong>
                                <span class="text-muted"> / {{ $instructor->title }}</span>
                            </dd>
                        @endforeach
                    </dl>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Issuing & Lifecycle Actions</h2>
                    <p>Status transitions and irreversible operations</p>
                </div>
            </div>
            <div class="card-body">
                @if ($certificate->isDraft())
                    <form method="post" action="{{ route('admin.certificates.issue', $certificate) }}"
                          onsubmit="return confirm('Issue this certificate? A permanent certificate number, QR code and PDF will be generated, and the data becomes read-only.')">
                        @csrf
                        <p class="text-muted" style="font-size: 0.8125rem;">Issuing generates the certificate number, unique verification code, renders the official PDF document, and locks the record against edits.</p>
                        <button class="btn btn-gold btn-lg" type="submit">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>Issue Certificate Now</span>
                        </button>
                    </form>
                @elseif ($certificate->isIssued())
                    @if (auth()->user()?->isSuperAdmin())
                        <form method="post" action="{{ route('admin.certificates.revoke', $certificate) }}"
                              onsubmit="return confirm('Revoke this certificate? The public verification page will immediately show it as revoked.')">
                            @csrf
                            <div class="field">
                                <label for="revoke_reason">Revocation reason</label>
                                <input id="revoke_reason" class="input" name="revoke_reason" type="text" required minlength="3" maxlength="255"
                                       placeholder="e.g., Incorrect spelling of student name / withdrawn">
                            </div>
                            <button class="btn btn-danger" type="submit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                                <span>Revoke Certificate</span>
                            </button>
                        </form>
                    @else
                        <p class="text-muted mb-0">Only a Super Admin can revoke an issued certificate. Please contact your administrator if changes are required.</p>
                    @endif
                @else
                    <p class="text-muted mb-0">Revoked certificates cannot be edited or re-issued. Create a new corrected certificate if needed.</p>
                @endif

                @if (($certificate->isDraft() || $certificate->isRevoked()) && auth()->user()?->isSuperAdmin())
                    <div style="height: 1px; background: var(--color-border); margin: var(--space-5) 0;"></div>
                    <form method="post" action="{{ route('admin.certificates.destroy', $certificate) }}"
                          onsubmit="return confirm('Delete this certificate permanently?')">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-sm" type="submit">
                            {{ $certificate->isDraft() ? 'Delete Draft Permanently' : 'Delete Revoked Certificate' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
