@extends('verify.layout')

@section('title', 'Verify a certificate — ' . $school['name'])

@section('styles')
    <style>
        .verify-page {
            width: 100%;
            max-width: 680px;
            margin: 28px auto 0;
        }

        .gov-header-actions .btn-outline { display: none; }

        .verify-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .verify-heading {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 24px 28px;
            border-bottom: 1px solid #e2e8f0;
        }

        .verify-heading-icon {
            display: grid;
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            place-items: center;
            color: #1e3a5f;
            background: #f1f5f9;
            border: 1px solid #dbe3ec;
            border-radius: 10px;
        }

        .verify-heading h1 {
            margin: 0;
            color: #0f2744;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -.02em;
            line-height: 1.3;
        }

        .verify-heading p {
            margin: 4px 0 0;
            color: #526174;
            font-size: 14px;
            line-height: 1.5;
        }

        .verify-content { padding: 26px 28px 28px; }
        .verify-description { margin: 0 0 20px; color: #475569; font-size: 14px; line-height: 1.65; }
        .verify-label { display: block; margin-bottom: 7px; color: #26384d; font-size: 13px; font-weight: 650; }
        .verify-input-wrap { position: relative; }
        .verify-input-wrap svg { position: absolute; top: 50%; left: 14px; color: #748399; transform: translateY(-50%); pointer-events: none; }
        .verify-input {
            display: block;
            width: 100%;
            min-height: 48px;
            padding: 11px 14px 11px 42px;
            color: #0f2744;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-family: var(--font-mono);
            font-size: 15px;
            font-weight: 600;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .verify-input::placeholder { color: #94a3b8; font-weight: 500; }
        .verify-input:focus { outline: none; border-color: #31577d; box-shadow: 0 0 0 3px rgba(49, 87, 125, .14); }
        .verify-error { display: flex; align-items: flex-start; gap: 7px; margin-top: 8px; color: #b42318; font-size: 13px; line-height: 1.45; }
        .verify-error svg { flex: 0 0 15px; margin-top: 1px; }

        .verify-actions { display: flex; gap: 10px; margin-top: 16px; }
        .verify-button, .verify-link {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1px solid transparent;
            border-radius: 7px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 650;
            line-height: 1.2;
            text-decoration: none;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .verify-button { color: #fff; background: #17385b; border-color: #17385b; }
        .verify-button:hover { background: #102b49; border-color: #102b49; }
        .verify-link { color: #334155; background: #fff; border-color: #cbd5e1; }
        .verify-link:hover { background: #f8fafc; border-color: #94a3b8; }
        .verify-button:focus-visible, .verify-link:focus-visible { outline: 3px solid rgba(49, 87, 125, .3); outline-offset: 2px; }

        .verify-privacy {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 20px;
            padding: 13px 14px;
            color: #526174;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            font-size: 12.5px;
            line-height: 1.55;
        }

        .verify-privacy svg { flex: 0 0 16px; margin-top: 1px; color: #64748b; }
        .verify-privacy strong { color: #334155; }

        @media (max-width: 600px) {
            .verify-page { margin-top: 12px; }
            .verify-heading { align-items: flex-start; padding: 20px; }
            .verify-heading-icon { width: 40px; height: 40px; flex-basis: 40px; }
            .verify-heading h1 { font-size: 19px; }
            .verify-content { padding: 20px; }
            .verify-actions { flex-direction: column; }
            .verify-button, .verify-link { width: 100%; }
        }
    </style>
@endsection

@section('content')
    <section class="verify-page" aria-labelledby="verify-title">
        <div class="verify-card">
            <div class="verify-heading">
                <div class="verify-heading-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7.5" />
                        <path d="m16.5 16.5 4 4" />
                    </svg>
                </div>
                <div>
                    <h1 id="verify-title">Verify a certificate</h1>
                    <p>Check a certificate issued by {{ $school['name'] }}.</p>
                </div>
            </div>

            <div class="verify-content">
                <p class="verify-description">Enter the registration number printed on the certificate. You can also open this page by scanning its verification QR code.</p>

                <form method="POST" action="{{ route('verify.lookup') }}">
                    @csrf
                    <label class="verify-label" for="certificate_no">Certificate registration number</label>
                    <div class="verify-input-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 3h7l5 5v13H5V3h2Z" />
                            <path d="M14 3v6h6M8 13h8M8 17h8" />
                        </svg>
                        <input
                            class="verify-input"
                            id="certificate_no"
                            name="certificate_no"
                            type="text"
                            value="{{ old('certificate_no') }}"
                            placeholder="e.g. {{ $school['number_prefix'] ?? 'EOC' }}-2026-000001"
                            autocomplete="off"
                            autofocus
                            aria-describedby="certificate_no_error"
                        >
                    </div>
                    @error('certificate_no')
                        <div class="verify-error" id="certificate_no_error" role="alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4m0 4h.01" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="verify-actions">
                        <button class="verify-button" type="submit">
                            Verify certificate
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14m-7-7 7 7-7 7" />
                            </svg>
                        </button>
                        <a class="verify-link" href="{{ $school['website'] ?? config('school.website', 'https://eminentoverseas.uk') }}" target="_blank" rel="noopener noreferrer">
                            Visit school website
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 3h7v7m-1-6L10 14" />
                                <path d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                            </svg>
                        </a>
                    </div>
                </form>

                <aside class="verify-privacy">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    </svg>
                    <div><strong>Privacy:</strong> The public record confirms certificate details without showing private contact information or date of birth.</div>
                </aside>
            </div>
        </div>
    </section>
@endsection
