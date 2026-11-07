<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorSecurityController extends Controller
{
    public function show(Request $request, QrCodeService $qr): View
    {
        $user = $request->user();
        $pendingSecret = blank($user->two_factor_confirmed_at) ? $user->two_factor_secret : null;

        return view('admin.security', [
            'enabled' => $user->hasEnabledTwoFactor(),
            'pendingSecret' => $pendingSecret,
            'qrCode' => $pendingSecret
                ? $qr->svgBase64(app(TwoFactorService::class)->provisioningUri($user, $pendingSecret))
                : null,
        ]);
    }

    public function enable(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasEnabledTwoFactor()) {
            return back()->with('error', 'Two-factor authentication is already enabled.');
        }

        $user->forceFill([
            'two_factor_secret' => $twoFactor->generateSecret(),
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()
            ->route('admin.security.show')
            ->with('status', 'Scan the QR code, then confirm with a six-digit code.');
    }

    public function confirm(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        $user = $request->user();
        $secret = (string) $user->two_factor_secret;

        if ($secret === '' || ! $twoFactor->verify($secret, (string) $request->input('code'))) {
            return back()->withErrors(['code' => 'That code is not valid. Check your authenticator app and try again.']);
        }

        $codes = $twoFactor->recoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => $codes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.security.show')
            ->with('status', 'Two-factor authentication is enabled.')
            ->with('recovery_codes', $codes);
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()
            ->route('admin.security.show')
            ->with('status', 'Two-factor authentication was disabled.');
    }
}
