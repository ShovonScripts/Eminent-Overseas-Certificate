@extends('verify.layout')

@section('title', 'Certificate Revoked — ' . $certificate->certificate_no)

@section('content')
<div class="credential-dossier-card">
    <div class="verification-status-bar is-revoked">
        <div class="status-badge-group">
            <div class="status-icon-circle" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
            </div>
            <div class="status-title-wrap">
                <h1>Certificate Has Been Revoked (失効)</h1>
                <p>This certificate record has been officially revoked by {{ $school['name'] }} and is invalid for immigration or academic use.</p>
            </div>
        </div>
        <div class="status-meta-pill">
            <div class="status-record-badge" style="background: rgba(254, 202, 202, 0.25); border-color: rgba(254, 202, 202, 0.4);">
                <span>Status: Revoked</span>
            </div>
            <div class="status-verified-timestamp">Record ID: {{ $code }}</div>
        </div>
    </div>

    <div class="dossier-body">
        <div class="cert-ref-header" style="background: #FEF2F2; border-color: #FECACA;">
            <div class="cert-no-block">
                <span class="cert-no-label" style="color: #991B1B;">Certificate Registration Number (証明書番号)</span>
                <div class="cert-no-value-row">
                    <span class="cert-no-pill mono-val" style="color: #991B1B; background: #FFFFFF; border-color: #FECACA;">{{ $certificate->certificate_no }}</span>
                    <button type="button" class="btn-copy-chip" onclick="copyText('{{ $certificate->certificate_no }}', 'Certificate Number')" title="Copy Certificate Number">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                        </svg>
                        <span>Copy</span>
                    </button>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                <span class="cert-status-tag is-revoked">&bull; Revoked &amp; Invalid (失効済)</span>
                @if ($certificate->revoked_at)
                    <span style="font-size: 11.5px; color: #991B1B; font-weight: 600;">Revoked On: {{ $certificate->revoked_at->format('Y-m-d H:i') }}</span>
                @endif
            </div>
        </div>

        <div class="dossier-section">
            <div class="dossier-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Revocation Notice &amp; Privacy Statement</span>
            </div>

            <p style="font-size: 14px; color: var(--color-gov-slate); line-height: 1.6; margin-bottom: 12px;">
                The credential associated with this registration number was officially invalidated by the institution administration. Under institutional records policy and international data protection standards, personal student details and academic ledgers are suppressed for revoked credentials.
            </p>

            @if ($certificate->revoke_reason)
                <div class="hours-metric-card" style="background: #FEF2F2; border-color: #FECACA; margin-bottom: 16px;">
                    <span class="metric-label" style="color: #991B1B;">Official Revocation Reason</span>
                    <span style="font-size: 14px; font-weight: 600; color: #991B1B; margin-top: 4px;">{{ $certificate->revoke_reason }}</span>
                </div>
            @endif

            <div class="privacy-sealed-notice">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>If you represent an embassy, visa authority, or educational institution requiring formal clarification regarding this revoked record, please contact the Office of the Registrar directly.</span>
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

            <a href="{{ route('verify.form') }}" class="btn-action btn-verify-more" title="Verify another certificate">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Verify Another Certificate</span>
            </a>
        </div>
    </div>
</div>
@endsection
