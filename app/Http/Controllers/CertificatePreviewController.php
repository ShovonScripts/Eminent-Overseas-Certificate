<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateIssuer;
use Illuminate\Http\Response;

class CertificatePreviewController extends Controller
{
    public function __invoke(Certificate $certificate, CertificateIssuer $issuer): Response
    {
        $this->authorize('view', $certificate);

        $pdfContent = $issuer->preview($certificate);
        $filename = 'preview-'.($certificate->certificate_no ?? $certificate->id).'.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
