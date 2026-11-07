@extends('layouts.admin')

@section('title', 'Certificates')
@section('eyebrow', 'Issuing & Management')

@section('topbar_actions')
    <a class="btn btn-secondary" href="{{ route('admin.certificates.import.form') }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        <span>Import CSV</span>
    </a>
    <a class="btn btn-primary" href="{{ route('admin.certificates.create') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>New Certificate</span>
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="toolbar">
            <div class="flex items-center gap-2" style="flex-wrap: wrap;">
                <a class="btn {{ $status === null ? 'btn-primary' : 'btn-secondary' }} btn-sm" href="{{ route('admin.certificates.index', array_filter(['q' => $search ?: null])) }}">
                    All <span style="opacity: 0.8; font-weight: 500;">({{ $counts['all'] }})</span>
                </a>
                <a class="btn {{ $status === \App\Enums\CertificateStatus::Draft ? 'btn-primary' : 'btn-secondary' }} btn-sm" href="{{ route('admin.certificates.index', array_filter(['status' => 'draft', 'q' => $search ?: null])) }}">
                    Drafts <span style="opacity: 0.8; font-weight: 500;">({{ $counts['draft'] }})</span>
                </a>
                <a class="btn {{ $status === \App\Enums\CertificateStatus::Issued ? 'btn-primary' : 'btn-secondary' }} btn-sm" href="{{ route('admin.certificates.index', array_filter(['status' => 'issued', 'q' => $search ?: null])) }}">
                    Issued <span style="opacity: 0.8; font-weight: 500;">({{ $counts['issued'] }})</span>
                </a>
                <a class="btn {{ $status === \App\Enums\CertificateStatus::Revoked ? 'btn-primary' : 'btn-secondary' }} btn-sm" href="{{ route('admin.certificates.index', array_filter(['status' => 'revoked', 'q' => $search ?: null])) }}">
                    Revoked <span style="opacity: 0.8; font-weight: 500;">({{ $counts['revoked'] }})</span>
                </a>
            </div>

            <div class="spacer"></div>

            <form class="flex items-center gap-2" method="get" action="{{ route('admin.certificates.index') }}">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status->value }}">
                @endif
                <div style="position: relative;">
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Search certificate #, ULID or student..." style="min-width: 280px; padding-left: 36px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <button class="btn btn-secondary" type="submit">Search</button>
                @if ($search !== '')
                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.certificates.index', $status ? ['status' => $status->value] : []) }}">Clear</a>
                @endif
            </form>
        </div>

        @if ($certificates->isEmpty())
            <div class="empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <circle cx="12" cy="8" r="6"/>
                    <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/>
                </svg>
                <strong>No certificates match your query</strong>
                @if ($search !== '' || $status)
                    <p class="text-muted" style="margin-bottom: 16px;">Try adjusting your filter or search keywords.</p>
                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.certificates.index') }}">Reset filters</a>
                @else
                    <p class="text-muted" style="margin-bottom: 16px;">Get started by issuing the first student certificate.</p>
                    <a class="btn btn-primary" href="{{ route('admin.certificates.create') }}">Create First Certificate</a>
                @endif
            </div>
        @else
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Certificate</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Study Period</th>
                        <th>Issued On</th>
                        <th>Status</th>
                        <th class="actions">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($certificates as $certificate)
                        <tr>
                            <td>
                                <a class="cell-title" href="{{ route('admin.certificates.show', $certificate) }}">
                                    {{ $certificate->certificate_no ?? 'Draft #'.$certificate->id }}
                                </a>
                                @if ($certificate->verification_code)
                                    <div class="cell-sub mono" style="font-size: 0.7rem; color: #64748B;">{{ $certificate->verification_code }}</div>
                                @endif
                            </td>
                            <td>
                                <strong style="color: #0F172A;">{{ $certificate->student_name_en }}</strong>
                                @if ($certificate->student_name_ja)
                                    <div class="cell-sub">{{ $certificate->student_name_ja }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $certificate->course?->name_en }}</div>
                                <span class="badge badge-muted" style="font-size: 0.65rem; padding: 1px 6px; margin-top: 2px;">{{ $certificate->course?->level_code }}</span>
                            </td>
                            <td class="text-muted" style="font-size: var(--text-xs); white-space: nowrap;">
                                {{ $certificate->study_start?->format('Y-m-d') }} &rarr; {{ $certificate->study_end?->format('Y-m-d') }}
                            </td>
                            <td class="text-muted" style="font-size: var(--text-xs);">
                                {{ $certificate->issued_on?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $certificate->status->value }}">{{ $certificate->status->label() }}</span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-secondary btn-sm" href="{{ route('admin.certificates.show', $certificate) }}">View</a>
                                @if ($certificate->isDraft())
                                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.certificates.edit', $certificate) }}">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
@endsection
