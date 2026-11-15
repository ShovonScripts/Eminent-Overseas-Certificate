<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, AdminActivityLogger $logger): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $logger->logLogin($request);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request, AdminActivityLogger $logger): RedirectResponse
    {
        $logger->logout($request);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
