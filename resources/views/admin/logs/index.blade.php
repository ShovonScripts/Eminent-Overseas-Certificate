@extends('layouts.admin')

@section('title', 'Verification Logs')
@section('eyebrow', 'Public Scans & Audit Trail')

@section('content')
    <div class="card">
        <div class="toolbar">
            <div class="flex items-center gap-2" style="flex-wrap: wrap;">
                <a class="btn {{ $result === '' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
                   href="{{ route('admin.logs.index', array_filter(['q' => $search ?: null])) }}">
                    All Queries
                </a>
                <a class="btn {{ $result === 'valid' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
                   href="{{ route('admin.logs.index', array_filter(['result' => 'valid', 'q' => $search ?: null])) }}">
                    Valid <span style="opacity: 0.8; font-weight: 500;">({{ $counts['valid'] }})</span>
                </a>
                <a class="btn {{ $result === 'revoked' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
                   href="{{ route('admin.logs.index', array_filter(['result' => 'revoked', 'q' => $search ?: null])) }}">
                    Revoked <span style="opacity: 0.8; font-weight: 500;">({{ $counts['revoked'] }})</span>
                </a>
                <a class="btn {{ $result === 'not_found' ? 'btn-primary' : 'btn-secondary' }} btn-sm"
                   href="{{ route('admin.logs.index', array_filter(['result' => 'not_found', 'q' => $search ?: null])) }}">
                    Not Found <span style="opacity: 0.8; font-weight: 500;">({{ $counts['not_found'] }})</span>
                </a>
            </div>

            <div class="spacer"></div>

            <form class="flex items-center gap-2" method="get" action="{{ route('admin.logs.index') }}">
                @if ($result !== '')
                    <input type="hidden" name="result" value="{{ $result }}">
                @endif
                <div style="position: relative;">
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Code, cert no. or IP..." style="min-width: 260px; padding-left: 36px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <button class="btn btn-secondary" type="submit">Search</button>
                @if ($search !== '')
                    <a class="btn btn-ghost btn-sm" href="{{ route('admin.logs.index', $result ? ['result' => $result] : []) }}">Clear</a>
                @endif
            </form>
        </div>

        @if ($logs->isEmpty())
            <div class="empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                </svg>
                <strong>No verification attempts recorded</strong>
                <p class="text-muted">Every scan of a QR code or manual lookup on the public verify portal will be permanently recorded here.</p>
            </div>
        @else
            <div class="table-wrap" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Code / Query Tried</th>
                        <th>Matched Certificate</th>
                        <th>Verification Result</th>
                        <th>IP Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="text-muted" style="font-size: var(--text-xs); white-space: nowrap;">
                                {{ $log->created_at?->format('Y-m-d H:i:s') }}
                                <div class="cell-sub">{{ $log->created_at?->diffForHumans() }}</div>
                            </td>
                            <td class="mono" style="font-weight: 600;">{{ $log->code_tried }}</td>
                            <td>
                                @if ($log->certificate)
                                    <a class="cell-title" href="{{ route('admin.certificates.show', $log->certificate) }}">
                                        {{ $log->certificate->certificate_no }}
                                    </a>
                                    <div class="cell-sub">{{ $log->certificate->student_name_en }}</div>
                                @else
                                    <span class="text-muted">Unrecognized code</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $log->result === 'valid' ? 'badge-issued' : ($log->result === 'revoked' ? 'badge-revoked' : 'badge-muted') }}">
                                    {{ str_replace('_', ' ', $log->result) }}
                                </span>
                            </td>
                            <td class="mono" style="font-size: var(--text-xs); color: #64748B;">{{ $log->ip_address ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
