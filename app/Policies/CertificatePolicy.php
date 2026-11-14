<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Certificate $certificate): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Certificate $certificate): bool
    {
        return $certificate->isDraft();
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return ($certificate->isDraft() || $certificate->isRevoked()) && $user->isSuperAdmin();
    }

    public function issue(User $user, Certificate $certificate): bool
    {
        return $certificate->isDraft();
    }

    public function download(User $user, Certificate $certificate): bool
    {
        return $certificate->isIssued() || $certificate->isRevoked();
    }

    public function revoke(User $user, Certificate $certificate): bool
    {
        return $certificate->isIssued() && $user->isSuperAdmin();
    }
}
