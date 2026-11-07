<?php

declare(strict_types=1);

namespace App\Enums;

enum CertificateStatus: string
{
    case Draft = 'draft';
    case Issued = 'issued';
    case Revoked = 'revoked';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Issued => 'Issued',
            self::Revoked => 'Revoked',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Issued => 'success',
            self::Revoked => 'danger',
        };
    }
}
