@extends('layouts.guest')

@section('title', 'Reset password')

@section('body')
    <div class="auth-page">
        <section class="auth-art">
            <div class="brand-lockup">
                <img src="{{ asset($schoolConfig['images']['logo']) }}" alt="{{ $schoolConfig['name'] }}">
                <div>
                    <div class="name">{{ $schoolConfig['name'] }}</div>
                    <div class="sub">Certificate Administration Portal</div>
                </div>
            </div>

            <div class="pitch">
                <h1>Restore access to your staff account.</h1>
                <p>Enter the email address associated with your staff account. We will send a secure, one-time password reset link to your inbox.</p>
            </div>

            <div class="meta">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Authorized Staff Access Only &middot; {{ $schoolConfig['address'] }}</span>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="eyebrow">Staff Portal</div>
                <h1>Forgot Password</h1>
                <p class="lede">Enter your registered email address to receive password reset instructions.</p>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="post" action="{{ route('password.email') }}" novalidate>
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" class="input" value="{{ old('email') }}"
                               placeholder="your.email@eminentoverseas.com"
                               required autofocus autocomplete="username">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-4);">
                        <span>Send Password Reset Link</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </button>
                </form>

                <div class="hint">
                    <a href="{{ route('login') }}" style="font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                        &larr; Back to sign in
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
