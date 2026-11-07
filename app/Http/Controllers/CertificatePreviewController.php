<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateIssuer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificatePreviewController extends Controller
{
    public function __invoke(Certificate $certificate, CertificateIssuer $issuer): StreamedResponse
    {
        $this->authorize('view', $certificate);

        $filename = 'preview-'.($certificate->certificate_no ?? $certificate->id).'.pdf';

        return response()->streamDownload(
            fn () => print $issuer->preview($certificate),
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }
}
