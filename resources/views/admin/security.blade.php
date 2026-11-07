@extends('layouts.admin')

@section('title', 'Security')
@section('eyebrow', 'Staff Account Security')

@section('content')
    <div class="card">
        <div class="card-head">
            <div>
                <h2>Two-Factor Authentication (2FA)</h2>
                <p>Add a time-based one-time password (TOTP) verification step via Google Authenticator, Microsoft Authenticator, or 1Password</p>
            </div>
            <span class="badge {{ $enabled ? 'badge-issued' : 'badge-muted' }}">
                {{ $enabled ? 'Enabled' : 'Not enabled' }}
            </span>
        </div>

        <div class="card-body">
            @if (session('recovery_codes'))
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <div>
                        <strong style="display: block; font-size: 0.9375rem; margin-bottom: 2px;">Save these emergency recovery codes now.</strong>
                        <p class="mb-0 text-muted" style="color: #065F46;">Each code can be used once if you lose access to your authenticator device. They will never be shown again.</p>
                    </div>
                </div>
                <div class="recovery-codes mono">
                    @foreach (session('recovery_codes') as $code)
                        <span>{{ $code }}</span>
                    @endforeach
                </div>
            @endif

            @if ($enabled)
                <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: var(--radius-md); padding: var(--space-4) var(--space-5); margin-bottom: var(--space-6); display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #059669; color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <strong style="color: #065F46; font-size: 0.9375rem;">Two-factor authentication is active</strong>
                        <p class="mb-0" style="color: #047857; font-size: 0.8125rem;">Active for <strong>{{ auth()->user()->email }}</strong>. You will be prompted for a 6-digit code on each sign-in.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('admin.security.disable') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="field">
                            <label for="password">Confirm Current Password to Disable</label>
                            <input id="password" class="input" name="password" type="password" required autocomplete="current-password" placeholder="Enter password">
                            @error('password')<div class="error-text">{{ $message }}</div>@enderror
                            <div class="help">Required to turn off two-factor authentication safeguards.</div>
                        </div>
                    </div>

                    <div class="form-actions" style="border-radius: var(--radius-md); margin-top: var(--space-4);">
                        <button class="btn btn-danger" type="submit">Disable Two-Factor Authentication</button>
                    </div>
                </form>
            @elseif ($pendingSecret)
                <div class="two-factor-setup">
                    <div>
                        <h3 style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 24px; height: 24px; border-radius: 50%; background: #2563EB; color: #FFF; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                            Scan QR Code
                        </h3>
                        @if ($qrCode)
                            <img class="qr-image" src="data:image/svg+xml;base64,{{ $qrCode }}" alt="Two-factor setup QR code">
                        @endif
                        <p class="text-muted mt-2" style="font-size: 0.8125rem;">Or enter this manual secret key into your app:</p>
                        <div class="secret-key mono">{{ $pendingSecret }}</div>
                    </div>
                    <div>
                        <h3 style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 24px; height: 24px; border-radius: 50%; background: #2563EB; color: #FFF; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                            Confirm with 6-Digit Code
                        </h3>
                        <p class="text-muted" style="font-size: 0.8125rem;">Enter the verification code currently generated by your authenticator app.</p>

                        <form method="post" action="{{ route('admin.security.confirm') }}">
                            @csrf
                            <div class="field">
                                <label for="code">6-Digit TOTP Code</label>
                                <input id="code" class="input mono" name="code" type="text" inputmode="numeric"
                                       pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code"
                                       placeholder="123456" style="letter-spacing: 0.2em; font-size: 1.1rem; max-width: 220px;">
                                @error('code')<div class="error-text">{{ $message }}</div>@enderror
                            </div>
                            <button class="btn btn-primary" type="submit">Confirm and Activate</button>
                        </form>
                    </div>
                </div>
            @else
                <div style="background: #F8FAFC; border: 1px dashed var(--color-border); border-radius: var(--radius-lg); padding: var(--space-6); text-align: center; max-width: 520px; margin: 0 auto;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <strong style="display: block; font-size: 1.0625rem; color: #0F172A; margin-bottom: 6px;">Protect Your Administrator Account</strong>
                    <p class="text-muted" style="font-size: 0.8125rem; margin-bottom: 20px;">Two-factor authentication adds an essential extra layer of defense against unauthorized access to the certificate issuing system.</p>

                    <form method="post" action="{{ route('admin.security.enable') }}">
                        @csrf
                        <button class="btn btn-primary btn-lg" type="submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                            </svg>
                            <span>Set Up Two-Factor Authentication</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
