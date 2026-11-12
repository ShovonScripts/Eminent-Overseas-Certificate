<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $settings = Settings::values();

        return view('admin.settings.edit', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, AdminActivityLogger $logger): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $oldSettings = Settings::values();

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_address' => ['required', 'string', 'max:500'],
            'school_phone' => ['required', 'string', 'max:50'],
            'school_email' => ['required', 'email', 'max:255'],
            'verify_base_url' => ['required', 'url', 'max:255'],
            'number_prefix' => ['required', 'string', 'max:10'],
            'logo_path' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
            'background_path' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
            'seal_path' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
            'divider_path' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2048'],
            'reset_logo' => ['nullable', 'boolean'],
            'reset_background' => ['nullable', 'boolean'],
            'reset_seal' => ['nullable', 'boolean'],
            'reset_divider' => ['nullable', 'boolean'],
        ]);

        $resetMap = [
            'reset_logo' => 'logo_path',
            'reset_background' => 'background_path',
            'reset_seal' => 'seal_path',
            'reset_divider' => 'divider_path',
        ];

        foreach ($resetMap as $flag => $settingKey) {
            if ($request->boolean($flag)) {
                Settings::where('key', $settingKey)->delete();
            }
        }

        Settings::flushCache();

        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $resetMap) || $value === null) {
                continue;
            }

            if ($value instanceof UploadedFile) {
                $path = $value->store('images/certificate', 'public');
                Settings::set($key, '/storage/'.$path);
            } else {
                Settings::set($key, (string) $value);
            }
        }

        $newSettings = Settings::values();

        $logger->logSettingsChanged($oldSettings, $newSettings, $request);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Settings updated successfully.');
    }
}
