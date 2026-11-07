<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class CertificateNumberGenerator
{
    public function make(CarbonInterface $issuedOn): string
    {
        return DB::transaction(function () use ($issuedOn): string {
            $prefix = (string) config('school.number_prefix', 'EOC');
            $year = $issuedOn->year;

            $last = Certificate::withTrashed()
                ->where('certificate_no', 'like', "{$prefix}-{$year}-%")
                ->lockForUpdate()
                ->max('certificate_no');

            $next = $last ? ((int) substr((string) $last, -6)) + 1 : 1;

            return sprintf('%s-%d-%06d', $prefix, $year, $next);
        });
    }
}
