@extends('verify.layout')

@section('title', 'Certificate Revoked')

@section('content')
    <div class="banner revoked">
        <div class="icon-wrapper">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
        </div>
        <div>
            <h1>Certificate Has Been Revoked</h1>
            <p>This certificate is no longer valid. Contact {{ $school['name'] }} for verification assistance.</p>
        </div>
    </div>
    <div class="content">
        <dl>
            <dt>Certificate No.</dt>
            <dd><span class="mono-val"
                    style="color: #991B1B; background: #FEF2F2; border-color: #FECACA;">{{ $certificate->certificate_no }}</span>
            </dd>

            <dt>Current Status</dt>
            <dd><span
                    style="color: #991B1B; background: #FEF2F2; padding: 2px 10px; border-radius: 9999px; font-size: 13px; font-weight: 700; border: 1px solid #FECACA;">&bull;
                    Revoked & Invalid</span></dd>

            @if ($certificate->revoked_at)
                <dt>Revoked On</dt>
                <dd>{{ $certificate->revoked_at->format('Y-m-d H:i') }}</dd>
            @endif
        </dl>

        <p class="muted" style="margin-top: 24px;">
            For student privacy and security compliance, personal details and academic summaries are not displayed for
            revoked credentials.
        </p>

        <a class="btn" href="{{ route('verify.form') }}">
            <span>Verify Another Certificate</span>
        </a>
    </div>
@endsection