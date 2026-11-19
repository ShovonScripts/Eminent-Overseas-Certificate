<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Certificate;

class CertificateObserver
{
    public function updating(Certificate $certificate): void
    {
        // Allow editing in all situations - certificates are no longer immutable
    }

    public function deleting(Certificate $certificate): void
    {
        // Allow deleting in all situations
    }
}
