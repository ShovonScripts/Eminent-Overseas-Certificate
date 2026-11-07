<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificatePdfService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateDownloadController extends Controller
{
    public function __invoke(Certificate $certificate, CertificatePdfService $pdf): Response|StreamedResponse
    {
        $this->authorize('download', $certificate);

        $filename = ($certificate->certificate_no ?? 'certificate').'.pdf';

        if ($certificate->pdf_path && Storage::disk('local')->exists($certificate->pdf_path)) {
            return Storage::disk('local')->download($certificate->pdf_path, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return response()->streamDownload(
            static fn () => print $pdf->render($certificate),
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }
}
