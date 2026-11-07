<?php

declare(strict_types=1);

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function svgBase64(string $url): string
    {
        return base64_encode(
            (string) QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($url)
        );
    }
}
