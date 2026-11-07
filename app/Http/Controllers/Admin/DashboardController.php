<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CertificateStatus;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\VerificationLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $since = now()->subDays(30);

        return view('admin.dashboard', [
            'courses' => Course::count(),
            'instructors' => Instructor::count(),
            'drafts' => Certificate::where('status', CertificateStatus::Draft)->count(),
            'issued' => Certificate::where('status', CertificateStatus::Issued)->count(),
            'revoked' => Certificate::where('status', CertificateStatus::Revoked)->count(),
            'verifications' => VerificationLog::where('created_at', '>=', $since)->count(),
            'recentCertificates' => Certificate::with(['course:id,name_en,level_code'])
                ->latest()
                ->limit(6)
                ->get(),
            'recentLogs' => VerificationLog::with('certificate:id,certificate_no')
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
