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
        $settings = Settings::pluck('value', 'key')->toArray();

        return view('admin.settings.edit', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, AdminActivityLogger $logger): RedirectResponse
    {
        $oldSettings = Settings::pluck('value', 'key')->toArray();

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_address' => ['required', 'string', 'max:500'],
            'school_phone' => ['required', 'string', 'max:50'],
            'school_email' => ['required', 'email', 'max:255'],
            'verify_base_url' => ['required', 'url', 'max:255'],
            'number_prefix' => ['required', 'string', 'max:10'],
            'logo_path' => ['nullable', 'file', 'image', 'max:2048'],
            'background_path' => ['nullable', 'file', 'image', 'max:2048'],
            'seal_path' => ['nullable', 'file', 'image', 'max:2048'],
        ]);

        foreach ($validated as $key => $value) {
            if ($value instanceof UploadedFile) {
                $path = $value->store('images/certificate', 'public');
                Settings::set($key, '/storage/'.$path);
            } else {
                Settings::set($key, $value);
            }
        }

        $newSettings = Settings::pluck('value', 'key')->toArray();

        $logger->logSettingsChanged($oldSettings, $newSettings, $request);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Settings updated successfully.');
    }
}
