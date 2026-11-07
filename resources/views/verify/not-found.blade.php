@extends('verify.layout')

@section('title', 'Certificate Not Found')

@section('content')
    <div class="banner neutral" style="background: linear-gradient(135deg, #1E293B 0%, #334155 100%);">
        <div class="icon-wrapper">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div>
            <h1>No Certificate Matches This Query</h1>
            <p>Please double-check the QR code or the certificate number and attempt the verification again.</p>
        </div>
    </div>
    <div class="content">
        <p class="muted" style="font-size: 14.5px;">
            The certificate code or number <strong style="color: #0F172A; font-family: var(--font-mono);">{{ $code ?? '' }}</strong> was not found in our institutional issuing database. A certificate that cannot be found here cannot be confirmed as an authentic credential.
        </p>

        <a class="btn" href="{{ route('verify.form') }}">
            <span>Search Another Certificate</span>
        </a>
    </div>
@endsection
