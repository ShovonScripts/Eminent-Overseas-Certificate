@extends('layouts.admin')

@section('title', 'Dashboard')
@section('eyebrow', 'Overview')

@section('topbar_actions')
    <a class="btn btn-gold" href="{{ route('admin.certificates.create') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Certificate</span>
    </a>
@endsection

@section('content')
    <div class="grid-4">
        <div class="stat">
            <div class="flex items-center justify-between">
                <div class="label">Total Certificates</div>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="6"/>
                        <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/>
                    </svg>
                </div>
            </div>
            <div class="value">{{ number_format($issued + $drafts + $revoked) }}</div>
            <div class="foot">
                <span class="badge badge-info" style="font-size: 0.6875rem;">{{ $courses }} courses</span>
                <span>in curriculum</span>
            </div>
        </div>

        <div class="stat">
            <div class="flex items-center justify-between">
                <div class="label">Issued & Active</div>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
            <div class="value" style="color: #059669;">{{ number_format($issued) }}</div>
            <div class="foot">
                <span class="badge badge-warning" style="font-size: 0.6875rem;">{{ $drafts }} drafts</span>
                <span>pending issue</span>
            </div>
        </div>

        <div class="stat">
            <div class="flex items-center justify-between">
                <div class="label">Revoked</div>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #FEF2F2; color: #EF4444; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
            </div>
            <div class="value" style="color: #DC2626;">{{ number_format($revoked) }}</div>
            <div class="foot">
                <span class="text-muted">Public scan shows revoked</span>
            </div>
        </div>

        <div class="stat">
            <div class="flex items-center justify-between">
                <div class="label">Public Verifications</div>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="value">{{ number_format($verifications) }}</div>
            <div class="foot">
                <span class="text-muted">Last 30 days &middot; {{ $instructors }} instructors</span>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Recent Certificates</h2>
                    <p>Latest drafts, issues and revocations</p>
                </div>
                <a class="btn btn-secondary btn-sm" href="{{ route('admin.certificates.index') }}">
                    <span>View all</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
            @if ($recentCertificates->isEmpty())
                <div class="empty">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                        <circle cx="12" cy="8" r="6"/>
                        <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/>
                    </svg>
                    <strong>No certificates created yet</strong>
                    <p class="text-muted" style="margin-bottom: 16px;">Create your first student certificate to get started.</p>
                    <a class="btn btn-primary btn-sm" href="{{ route('admin.certificates.create') }}">Create Certificate</a>
                </div>
            @else
                <div class="table-wrap" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Certificate</th>
                            <th>Student</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($recentCertificates as $certificate)
                            <tr>
                                <td>
                                    <a class="cell-title" href="{{ route('admin.certificates.show', $certificate) }}">
                                        {{ $certificate->certificate_no ?? 'Draft #'.$certificate->id }}
                                    </a>
                                    <div class="cell-sub">{{ $certificate->course?->name_en }} &middot; <span class="badge badge-muted" style="font-size: 0.65rem; padding: 1px 6px;">{{ $certificate->course?->level_code }}</span></div>
                                </td>
                                <td>
                                    <strong style="color: #0F172A;">{{ $certificate->student_name_en }}</strong>
                                    @if ($certificate->student_name_ja)
                                        <div class="cell-sub">{{ $certificate->student_name_ja }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $certificate->status->value }}">{{ $certificate->status->label() }}</span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Latest Verification Attempts</h2>
                    <p>Live audit trail of scanned QR codes and checks</p>
                </div>
                <a class="btn btn-secondary btn-sm" href="{{ route('admin.logs.index') }}">
                    <span>View logs</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
            @if ($recentLogs->isEmpty())
                <div class="empty">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                    </svg>
                    <strong>No checks recorded yet</strong>
                    <p class="text-muted">Public scans and verification queries will appear here in real time.</p>
                </div>
            @else
                <div class="table-wrap" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Code / Query</th>
                            <th>Result</th>
                            <th>When</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($recentLogs as $log)
                            <tr>
                                <td class="mono" style="font-weight: 500;">{{ $log->code_tried }}</td>
                                <td>
                                    <span class="badge {{ $log->result === 'valid' ? 'badge-issued' : ($log->result === 'revoked' ? 'badge-revoked' : 'badge-muted') }}">
                                        {{ str_replace('_', ' ', $log->result) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $log->created_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h2>Quick Actions</h2>
                <p>Frequent certification management workflows</p>
            </div>
        </div>
        <div class="card-body flex items-center gap-3" style="flex-wrap: wrap;">
            <a class="btn btn-primary" href="{{ route('admin.certificates.create') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Create New Certificate</span>
            </a>
            <a class="btn btn-secondary" href="{{ route('admin.certificates.import.form') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <span>Bulk Import (CSV)</span>
            </a>
            <a class="btn btn-secondary" href="{{ route('admin.courses.index') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                </svg>
                <span>Manage Courses</span>
            </a>
            <a class="btn btn-secondary" href="{{ route('admin.instructors.index') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
                <span>Manage Instructors</span>
            </a>
        </div>
    </div>
@endsection
