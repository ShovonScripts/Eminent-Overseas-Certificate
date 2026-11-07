<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificatePdfService
{
    public function __construct(private readonly QrCodeService $qr) {}

    public function generate(Certificate $certificate): string
    {
        $binary = $this->render($certificate);
        $path = 'certificates/'.($certificate->certificate_no ?? $certificate->id).'.pdf';

        Storage::disk('local')->put($path, $binary);

        $certificate->update([
            'pdf_path' => $path,
            'pdf_sha256' => hash('sha256', $binary),
        ]);

        return $path;
    }

    public function render(Certificate $certificate, bool $draft = false): string
    {
        $certificate->loadMissing(['course', 'instructors', 'attendances']);

        $qrBase64 = (! $draft && $certificate->verification_code)
            ? $this->qr->svgBase64($certificate->verifyUrl())
            : null;

        $pdf = Pdf::loadView('certificates.pdf', [
            'c' => $certificate,
            'school' => config('school'),
            'qrBase64' => $qrBase64,
            'draft' => $draft,
            'images' => [
                'background' => $this->dataUri((string) config('school.images.background')),
                'logo' => $this->dataUri((string) config('school.images.logo')),
                'seal' => $this->dataUri((string) config('school.images.seal')),
            ],
        ])
            ->setPaper('a4', 'portrait')
            ->setOption([
                'font_dir' => storage_path('fonts'),
                'font_cache' => storage_path('fonts'),
                'temp_dir' => storage_path('app/temp'),
                'chroot' => base_path(),
                'default_font' => 'NotoJP',
                'is_remote_enabled' => false,
                'is_font_subsetting_enabled' => true,
                'dpi' => 150,
            ]);

        $fontMetrics = $pdf->getFontMetrics();

        foreach ([
            'normal' => (string) config('school.fonts.regular'),
            'bold' => (string) config('school.fonts.bold'),
        ] as $weight => $file) {
            if (is_file($file)) {
                $fontMetrics->registerFont(
                    ['family' => 'NotoJP', 'weight' => $weight, 'style' => 'normal'],
                    str_replace('\\', '/', $file),
                );
            }
        }

        return $pdf->output();
    }

    private function dataUri(string $path): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }
}
