<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Str;

class VerificationCodeGenerator
{
    public function make(): string
    {
        do {
            $code = strtoupper((string) Str::ulid());
        } while (Certificate::withTrashed()->where('verification_code', $code)->exists());

        return $code;
    }
}
