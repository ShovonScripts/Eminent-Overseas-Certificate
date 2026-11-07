<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $result = (string) $request->query('result');
        $allowed = ['valid', 'revoked', 'not_found'];

        $logs = VerificationLog::query()
            ->with('certificate:id,certificate_no')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('code_tried', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('certificate', function ($query) use ($search): void {
                            $query->where('certificate_no', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($result, $allowed, true), fn ($query) => $query->where('result', $result))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.logs.index', [
            'logs' => $logs,
            'search' => $search,
            'result' => in_array($result, $allowed, true) ? $result : '',
            'counts' => [
                'valid' => VerificationLog::where('result', 'valid')->count(),
                'revoked' => VerificationLog::where('result', 'revoked')->count(),
                'not_found' => VerificationLog::where('result', 'not_found')->count(),
            ],
        ]);
    }
}
