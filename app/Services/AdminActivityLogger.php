<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogger
{
    public function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null
    ): void {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        AdminActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    public function logCreated(string $modelType, int $modelId, array $newValues, ?Request $request = null): void
    {
        $this->log('created', $modelType, $modelId, null, $newValues, $request);
    }

    public function logUpdated(string $modelType, int $modelId, array $oldValues, array $newValues, ?Request $request = null): void
    {
        $this->log('updated', $modelType, $modelId, $oldValues, $newValues, $request);
    }

    public function logDeleted(string $modelType, int $modelId, array $oldValues, ?Request $request = null): void
    {
        $this->log('deleted', $modelType, $modelId, $oldValues, null, $request);
    }

    public function logIssued(int $certificateId, ?Request $request = null): void
    {
        $this->log('issued', 'App\Models\Certificate', $certificateId, null, ['issued' => true], $request);
    }

    public function logRevoked(int $certificateId, string $reason, ?Request $request = null): void
    {
        $this->log('revoked', 'App\Models\Certificate', $certificateId, null, ['reason' => $reason], $request);
    }

    public function logImported(int $count, ?Request $request = null): void
    {
        $this->log('imported', 'App\Models\Certificate', null, null, ['imported_count' => $count], $request);
    }

    public function logLogin(?Request $request = null): void
    {
        $this->log('login', null, null, null, null, $request);
    }

    public function logout(?Request $request = null): void
    {
        $this->log('logout', null, null, null, null, $request);
    }

    public function logSettingsChanged(array $oldValues, array $newValues, ?Request $request = null): void
    {
        $this->log('settings_changed', 'App\Models\Settings', null, $oldValues, $newValues, $request);
    }
}
