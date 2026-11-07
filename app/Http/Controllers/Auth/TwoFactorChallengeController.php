<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $request->validate([
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        $user = User::find($request->session()->get('login.id'));

        if (! $user || ! $user->hasEnabledTwoFactor()) {
            $request->session()->forget(['login.id', 'login.remember']);

            return redirect()->route('login')->withErrors([
                'email' => 'Your session expired. Please sign in again.',
            ]);
        }

        $code = trim((string) $request->input('code'));
        $recoveryCode = $this->normalizeRecoveryCode((string) $request->input('recovery_code'));

        if ($code === '' && $recoveryCode === '') {
            throw ValidationException::withMessages([
                'code' => 'Enter the code from your authenticator app or a recovery code.',
            ]);
        }

        $verified = $code !== ''
            ? $twoFactor->verify((string) $user->two_factor_secret, $code)
            : $this->useRecoveryCode($user, $recoveryCode);

        if (! $verified) {
            throw ValidationException::withMessages([
                'code' => 'That code is not valid. Try the next code or use a recovery code.',
            ]);
        }

        $remember = (bool) $request->session()->get('login.remember', false);

        Auth::login($user, $remember);

        $request->session()->forget(['login.id', 'login.remember']);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    private function normalizeRecoveryCode(string $code): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper(trim($code))) ?? '';
    }

    private function useRecoveryCode(User $user, string $code): bool
    {
        if ($code === '') {
            return false;
        }

        $codes = $user->twoFactorRecoveryCodes();
        $index = array_search($code, $codes, true);

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);

        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        return true;
    }
}
