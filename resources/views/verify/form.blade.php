@extends('verify.layout')

@section('title', 'Verify Certificate Authenticity — ' . $school['name'])

@section('content')
<div class="credential-dossier-card">
    <div class="verification-status-bar is-neutral">
        <div class="status-badge-group">
            <div class="status-icon-circle" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>
            <div class="status-title-wrap">
                <h1>Verify a certificate</h1>
                <p>Public credential verification service for {{ $school['name'] }}.</p>
            </div>
        </div>
        <div class="status-meta-pill">
            <div class="status-record-badge">
                <span>Secure Query</span>
            </div>
            <div class="status-verified-timestamp">TLS 256-bit Encrypted</div>
        </div>
    </div>

    <div class="dossier-body">
        <div class="dossier-section">
            <div class="dossier-section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" x2="16" y1="2" y2="6"/>
                    <line x1="8" x2="8" y1="2" y2="6"/>
                    <line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
                <span>Search Credential Record (証明書照会)</span>
            </div>

            <p style="font-size: 14px; color: var(--color-gov-slate); line-height: 1.6; margin-bottom: 20px;">
                Enter the unique certificate registration number printed on the certificate (or scan the verification QR code) to access the official archival record.
            </p>

            <form method="POST" action="{{ route('verify.lookup') }}" style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div>
                    <label for="certificate_no" style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-gov-slate); margin-bottom: 6px;">
                        Certificate Registration Number (証明書番号)
                    </label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <input id="certificate_no" name="certificate_no" type="text" value="{{ old('certificate_no') }}"
                               placeholder="e.g. {{ $school['number_prefix'] ?? 'EOC' }}-2026-000001"
                               autocomplete="off" autofocus
                               style="width: 100%; padding: 13px 16px 13px 44px; font-family: var(--font-mono); font-size: 16px; font-weight: 700; border: 1.5px solid var(--color-gov-border); border-radius: var(--radius-sm); outline: none; transition: border-color 0.15s ease; color: var(--color-gov-navy); background: #FFFFFF;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             style="position: absolute; left: 15px; color: var(--color-gov-muted); pointer-events: none;">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <path d="M7 7h10"/>
                            <path d="M7 12h10"/>
                            <path d="M7 17h10"/>
                        </svg>
                    </div>
                    @error('certificate_no')
                        <div style="color: #DC2626; font-size: 13px; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <button type="submit" class="btn-action btn-website" style="padding: 13px 26px; font-size: 14px;">
                        <span>Verify Credential Authenticity</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                    <!-- Visit Official Website: pulls URL dynamically from /admin/settings -->
                    <a href="{{ $school['website'] ?? config('school.website', 'https://eminentoverseas.uk') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn-action btn-print"
                       style="padding: 13px 20px; font-size: 14px;"
                       title="Visit official school website">
                        <span>Visit Official Website</span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        <div class="privacy-sealed-notice" style="margin-top: 24px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <div>
                <strong>Student Privacy Protection:</strong> This public verification registry confirms student enrollment, course completion hours, attendance rates, and issuance integrity without exposing personal private information (such as birth dates, residential addresses, contact phones, or emails).
            </div>
        </div>
    </div>
</div>
@endsection

