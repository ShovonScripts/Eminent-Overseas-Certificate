@extends('verify.layout')

@section('title', 'Record Not Found — Credential Registry')

@section('content')
<div class="credential-dossier-card">
    <div class="verification-status-bar is-neutral">
        <div class="status-badge-group">
            <div class="status-icon-circle" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div class="status-title-wrap">
                <h1>Record Not Found in Registry (照会結果なし)</h1>
                <p>No valid certificate matching this registration query was found in the official records of {{ $school['name'] }}.</p>
            </div>
        </div>
        <div class="status-meta-pill">
            <div class="status-record-badge">
                <span>Result: Unverified</span>
            </div>
            <div class="status-verified-timestamp">Search Code: {{ $code ?? 'None' }}</div>
        </div>
    </div>

    <div class="dossier-body">
        <div class="dossier-section">
            <div class="dossier-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Registry Verification Notice</span>
            </div>

            <p style="font-size: 14.5px; color: var(--color-gov-slate); line-height: 1.6; margin-bottom: 16px;">
                The identifier <strong style="color: var(--color-gov-navy); font-family: var(--font-mono); background: #F1F5F9; padding: 2px 8px; border-radius: 4px; border: 1px solid var(--color-gov-border);">{{ $code ?? '' }}</strong> does not correspond to any issued credential in our central database. Any certificate that cannot be confirmed through this portal must be treated as unverified.
            </p>

            <div class="hours-metric-card" style="margin-bottom: 20px;">
                <span class="metric-label">Possible Reasons &amp; Recommended Actions</span>
                <ul style="font-size: 13px; color: var(--color-gov-slate); margin: 8px 0 0 18px; line-height: 1.6;">
                    <li>Check that the Certificate Number or QR code URL was copied or scanned accurately without missing characters.</li>
                    <li>Draft certificates not yet officially issued by the administration are unavailable in the public registry.</li>
                    <li>If you hold a physical certificate bearing this number, please contact {{ $school['name'] }} directly for manual verification.</li>
                </ul>
            </div>
        </div>

        <div class="dossier-actions">
            <div class="dossier-actions-left">
                <a href="{{ $school['website'] ?? config('school.website', 'https://eminentoverseas.uk') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn-action btn-website"
                   title="Visit official school website">
                    <span>Visit Official Website</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                </a>
            </div>

            <a href="{{ route('verify.form') }}" class="btn-action btn-print" title="Search registry again">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Search Another Certificate</span>
            </a>
        </div>
    </div>
</div>
@endsection

