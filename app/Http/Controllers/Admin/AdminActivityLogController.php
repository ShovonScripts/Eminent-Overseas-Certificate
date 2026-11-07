<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminActivityLogController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class); // Super admin only

        $logs = AdminActivityLog::with('user')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.activity-logs.index', [
            'logs' => $logs,
        ]);
    }

    public function show(AdminActivityLog $log): JsonResponse
    {
        $this->authorize('viewAny', User::class); // Super admin only

        $log->load('user');

        return response()->json([
            'id' => $log->id,
            'action' => $log->action,
            'action_color' => $log->actionColor(),
            'user' => $log->user ? [
                'name' => $log->user->name,
                'email' => $log->user->email,
            ] : null,
            'model_type' => $log->model_type,
            'model_id' => $log->model_id,
            'old_values' => $log->old_values,
            'new_values' => $log->new_values,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
        ]);
    }
}
