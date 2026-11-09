<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use App\Models\Settings;
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

        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $schoolConfig = config('school');
        $images = [
            'background' => $this->dataUri($this->resolveImagePath('background_path', $schoolConfig['images']['background'])),
            'logo' => $this->dataUri($this->resolveImagePath('logo_path', $schoolConfig['images']['logo'])),
            'seal' => $this->dataUri($this->resolveImagePath('seal_path', $schoolConfig['images']['seal'])),
            'divider' => $this->dataUri((string) ($schoolConfig['images']['divider'] ?? public_path('images/certificate/divider.png'))),
        ];

        $pdf = Pdf::loadView('certificates.pdf', [
            'c' => $certificate,
            'school' => $schoolConfig,
            'qrBase64' => $qrBase64,
            'draft' => $draft,
            'images' => $images,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption([
                'font_dir' => storage_path('fonts'),
                'font_cache' => storage_path('fonts'),
                'temp_dir' => $tempDir,
                'chroot' => base_path(),
                'default_font' => 'NotoJP',
                'is_remote_enabled' => false,
                'is_font_subsetting_enabled' => true,
                'dpi' => 150,
            ]);

        // Clear any previous font cache to pick up the new font paths
        $cacheFile = storage_path('fonts/fonts.json');
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }

        $fontMetrics = $pdf->getFontMetrics();

        // Register Noto Sans JP static fonts from configured resource paths
        $regularPath = (string) ($schoolConfig['fonts']['regular'] ?? resource_path('fonts/NotoSansJP-Regular.ttf'));
        $boldPath = (string) ($schoolConfig['fonts']['bold'] ?? resource_path('fonts/NotoSansJP-Bold.ttf'));

        if (is_file($regularPath)) {
            $fontMetrics->registerFont(
                ['family' => 'NotoJP', 'weight' => 'normal', 'style' => 'normal'],
                str_replace('\\', '/', $regularPath),
            );
        }

        if (is_file($boldPath)) {
            $fontMetrics->registerFont(
                ['family' => 'NotoJP', 'weight' => 'bold', 'style' => 'normal'],
                str_replace('\\', '/', $boldPath),
            );
        }

        $oldEnglishPath = resource_path('fonts/OldEnglish.ttf');
        if (is_file($oldEnglishPath)) {
            $fontMetrics->registerFont(
                ['family' => 'OldEnglish', 'weight' => 'normal', 'style' => 'normal'],
                str_replace('\\', '/', $oldEnglishPath),
            );
            $fontMetrics->registerFont(
                ['family' => 'OldEnglish', 'weight' => 'bold', 'style' => 'normal'],
                str_replace('\\', '/', $oldEnglishPath),
            );
        }

        return $pdf->output();
    }

    private function resolveImagePath(string $settingsKey, string $fallbackPath): string
    {
        $dbPath = Settings::get($settingsKey);
        if ($dbPath) {
            return $dbPath;
        }

        return $fallbackPath;
    }

    private function dataUri(string $path): ?string
    {
        if (str_starts_with($path, '/storage/')) {
            $path = Storage::disk('public')->path(substr($path, strlen('/storage/')));
        } elseif (str_starts_with($path, 'storage/')) {
            $path = Storage::disk('public')->path(substr($path, strlen('storage/')));
        }

        if (! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            default => null,
        };

        if (! in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/svg+xml'], true)) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($path));
    }
}
