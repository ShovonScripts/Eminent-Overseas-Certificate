<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\VerificationLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class VerifyController extends Controller
{
    public function form(): View
    {
        return view('verify.form', ['school' => config('school')]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'certificate_no' => ['required', 'string', 'max:40'],
        ]);

        $number = trim($validated['certificate_no']);
        $certificate = Certificate::where('certificate_no', $number)->first();

        $this->log($request, $certificate, $number, $certificate ? $certificate->status->value : 'not_found');

        if (! $certificate) {
            return redirect()
                ->route('verify.form')
                ->withInput()
                ->withErrors(['certificate_no' => 'No certificate matches that number.']);
        }

        return redirect()->route('verify.show', $certificate->verification_code);
    }

    public function show(Request $request, string $code): Response
    {
        $certificate = Certificate::with('course')
            ->where('verification_code', $code)
            ->select([
                'id',
                'certificate_no',
                'verification_code',
                'student_name_en',
                'student_name_ja',
                'student_date_of_birth',
                'student_gender',
                'student_email',
                'student_phone',
                'course_id',
                'study_start',
                'study_end',
                'hours_per_day',
                'days_per_week',
                'class_days',
                'national_holidays',
                'total_class_hours',
                'total_attendance_hours',
                'attendance_rate',
                'issued_on',
                'status',
                'issued_at',
                'revoked_at',
                'revoke_reason',
                'pdf_path',
                'pdf_sha256',
            ])
            ->where('verification_code', $code)
            ->first();

        $result = match (true) {
            ! $certificate => 'not_found',
            $certificate->status === CertificateStatus::Revoked => 'revoked',
            $certificate->status === CertificateStatus::Issued => 'valid',
            default => 'not_found',
        };

        $this->log($request, $certificate, $code, $result);

        $view = $result === 'not_found' ? 'verify.not-found' : "verify.{$result}";

        return response()
            ->view($view, [
                'certificate' => $certificate,
                'school' => config('school'),
                'code' => $code,
            ], $result === 'not_found' ? 404 : 200)
            ->withHeaders([
                'X-Robots-Tag' => 'noindex, nofollow',
                'Cache-Control' => 'no-store, max-age=0',
            ]);
    }

    private function log(Request $request, ?Certificate $certificate, string $codeTried, string $result): void
    {
        VerificationLog::create([
            'certificate_id' => $certificate?->id,
            'code_tried' => substr($codeTried, 0, 64),
            'result' => $result,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'created_at' => now(),
        ]);
    }
}
