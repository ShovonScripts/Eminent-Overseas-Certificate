<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\CertificateStatus;
use App\Models\Certificate;

class CertificateObserver
{
    public function updating(Certificate $certificate): void
    {
        $original = $certificate->getOriginal('status');
        $originalValue = $original instanceof CertificateStatus ? $original->value : $original;

        if (! in_array($originalValue, [CertificateStatus::Issued->value, CertificateStatus::Revoked->value], true)) {
            return;
        }

        $allowed = ['status', 'revoked_at', 'revoke_reason', 'updated_at'];
        $illegal = array_diff(array_keys($certificate->getDirty()), $allowed);

        if ($illegal !== []) {
            abort(422, 'Issued certificates cannot be edited. Revoke and re-issue.');
        }
    }

    public function deleting(Certificate $certificate): void
    {
        if (! $certificate->isDraft() && ! $certificate->isRevoked()) {
            abort(422, 'Only draft or revoked certificates can be deleted.');
        }
    }
}
