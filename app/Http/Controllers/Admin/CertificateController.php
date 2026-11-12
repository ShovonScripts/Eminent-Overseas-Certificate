<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CertificateStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateRequest;
use App\Http\Requests\ImportCertificatesRequest;
use App\Http\Requests\SuggestAttendanceRequest;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Services\AdminActivityLogger;
use App\Services\AttendanceSuggester;
use App\Services\CertificateCsvImporter;
use App\Services\CertificateIssuer;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;
use Throwable;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = CertificateStatus::tryFrom((string) $request->query('status'));

        $certificates = Certificate::query()
            ->with(['course:id,name_en,level_code'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('certificate_no', 'like', "%{$search}%")
                        ->orWhere('verification_code', 'like', "%{$search}%")
                        ->orWhere('student_name_en', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'search' => $search,
            'status' => $status,
            'counts' => [
                'all' => Certificate::count(),
                'draft' => Certificate::where('status', CertificateStatus::Draft)->count(),
                'issued' => Certificate::where('status', CertificateStatus::Issued)->count(),
                'revoked' => Certificate::where('status', CertificateStatus::Revoked)->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.certificates.form', [
            'certificate' => new Certificate([
                'study_start' => now()->subMonths(5)->startOfMonth(),
                'study_end' => now(),
                'issued_on' => now(),
                'hours_per_day' => 3,
                'days_per_week' => 3,
                'class_days' => ['Saturday', 'Monday', 'Wednesday'],
                'national_holidays' => '',
            ]),
            'courses' => Course::orderBy('level_code')->get(),
            'instructors' => Instructor::where('is_active', true)->orderBy('name')->get(['id', 'name', 'title']),
        ]);
    }

    public function importForm(): View
    {
        $this->authorize('create', Certificate::class);

        return view('admin.certificates.import');
    }

    public function import(ImportCertificatesRequest $request, CertificateCsvImporter $importer, AdminActivityLogger $logger): View|RedirectResponse
    {
        $this->authorize('create', Certificate::class);

        try {
            $results = $importer->import(
                $request->file('csv')->getRealPath(),
                $request->user(),
            );
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['csv' => $exception->getMessage()]);
        }

        if ($results['issued'] > 0) {
            $logger->logImported($results['issued'], $request);
        }

        return view('admin.certificates.import', ['results' => $results]);
    }

    public function importTemplate(): Response
    {
        $this->authorize('create', Certificate::class);

        $csv = implode("\n", [
            'student_name_en,student_name_ja,student_date_of_birth,student_gender,student_email,student_phone,course,study_start,study_end,issued_on,class_days,hours_per_day,national_holidays,instructors,2025-06,2025-07,2025-08',
            'Md. Anik Rahman,アニク・ラーマン,2004-12-09,male,anik@example.com,+8801711111111,N5,2025-06-02,2025-08-31,2025-09-15,Saturday;Monday;Wednesday,3,6月9日;6月11日,Taro Yamada;Hanako Suzuki,33,36,33',
        ]);

        return response($csv."\n", 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="certificate-import-template.csv"',
        ]);
    }

    public function store(CertificateRequest $request, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('create', Certificate::class);

        $certificate = $this->persist(new Certificate, $request->validated());

        $logger->logCreated('App\Models\Certificate', $certificate->id, $certificate->toArray(), $request);

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('status', 'Draft certificate saved. Review it, then issue.');
    }

    public function show(Certificate $certificate): View
    {
        $certificate->load(['course', 'attendances', 'instructors', 'issuedBy']);

        return view('admin.certificates.show', ['certificate' => $certificate]);
    }

    public function edit(Certificate $certificate): View
    {
        $this->authorize('update', $certificate);

        $certificate->load(['attendances', 'instructors']);

        return view('admin.certificates.form', [
            'certificate' => $certificate,
            'courses' => Course::orderBy('level_code')->get(),
            'instructors' => Instructor::where('is_active', true)
                ->orWhereIn('id', $certificate->instructors->pluck('id'))
                ->orderBy('name')
                ->get(['id', 'name', 'title']),
        ]);
    }

    public function update(CertificateRequest $request, Certificate $certificate, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('update', $certificate);

        $oldValues = $certificate->toArray();

        $this->persist($certificate, $request->validated());

        $logger->logUpdated('App\Models\Certificate', $certificate->id, $oldValues, $certificate->fresh()->toArray(), $request);

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('status', 'Draft certificate was updated.');
    }

    public function destroy(Certificate $certificate, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('delete', $certificate);

        try {
            $oldValues = $certificate->toArray();
            $certificate->delete();

            $logger->logDeleted('App\Models\Certificate', $certificate->id, $oldValues, request());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'This certificate cannot be deleted.');
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with('status', 'Draft certificate was deleted.');
    }

    public function suggest(SuggestAttendanceRequest $request, AttendanceSuggester $suggester): JsonResponse
    {
        $data = $request->validated();
        $start = CarbonImmutable::parse($data['study_start']);
        $end = CarbonImmutable::parse($data['study_end']);

        $holidays = $suggester->parseHolidays($data['national_holidays'] ?? null, $start->year, $end->year);

        $monthly = $suggester->suggest(
            $start,
            $end,
            $data['class_days'],
            (int) $data['hours_per_day'],
            $holidays,
        );

        $rows = [];

        foreach ($monthly as $yearMonth => $hours) {
            [$year, $month] = explode('-', $yearMonth);

            $rows[] = [
                'year' => (int) $year,
                'month' => (int) $month,
                'class_hours' => $hours,
            ];
        }

        return response()->json(['rows' => $rows]);
    }

    public function issue(Request $request, Certificate $certificate, CertificateIssuer $issuer, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('issue', $certificate);

        try {
            $issuer->issue($certificate, $request->user());

            $logger->logIssued($certificate->id, $request);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'The certificate could not be issued. Check the PDF settings and try again.');
        }

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('status', 'Certificate issued. The QR code and PDF are ready.');
    }

    public function revoke(Request $request, Certificate $certificate, CertificateIssuer $issuer, AdminActivityLogger $logger): RedirectResponse
    {
        $this->authorize('revoke', $certificate);

        $validated = $request->validate([
            'revoke_reason' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        try {
            $issuer->revoke($certificate, $validated['revoke_reason'], $request->user());

            $logger->logRevoked($certificate->id, $validated['revoke_reason'], $request);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()
            ->route('admin.certificates.show', $certificate)
            ->with('status', 'Certificate was revoked. Its QR page now shows the revoked status.');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function persist(Certificate $certificate, array $data): Certificate
    {
        return DB::transaction(function () use ($certificate, $data): Certificate {
            $certificate->fill(Arr::only($data, [
                'course_id',
                'student_name_en',
                'student_name_ja',
                'student_date_of_birth',
                'student_gender',
                'student_email',
                'student_phone',
                'study_start',
                'study_end',
                'hours_per_day',
                'days_per_week',
                'class_days',
                'national_holidays',
                'issued_on',
            ]));

            $certificate->save();

            $certificate->attendances()->delete();

            foreach ($data['attendances'] as $row) {
                $certificate->attendances()->create([
                    'year' => (int) $row['year'],
                    'month' => (int) $row['month'],
                    'class_hours' => (int) $row['class_hours'],
                    'attendance_hours' => (int) $row['attendance_hours'],
                ]);
            }

            $sync = [];

            foreach (array_values($data['instructors']) as $index => $instructorId) {
                $sync[(int) $instructorId] = ['position' => $index + 1];
            }

            $certificate->instructors()->sync($sync);

            return $certificate->refresh();
        });
    }
}
