<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_issued_certificate_can_be_edited(): void
    {
        $certificate = Certificate::factory()->issued()->create();

        $certificate->update(['study_end' => '2026-02-01']);

        $this->assertSame('2026-02-01', $certificate->fresh()->study_end->toDateString());
    }

    public function test_an_issued_certificate_can_be_deleted_by_super_admin(): void
    {
        $this->actingAs($this->createSuperAdmin());

        $certificate = Certificate::factory()->issued()->create();

        $certificate->delete();

        $this->assertSoftDeleted($certificate);
    }

    private function createSuperAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    public function test_a_revoked_certificate_can_be_deleted(): void
    {
        $certificate = Certificate::factory()->revoked()->create();

        $certificate->delete();

        $this->assertSoftDeleted($certificate);
    }

    public function test_a_draft_can_still_be_edited(): void
    {
        $certificate = Certificate::factory()->create();

        $certificate->update(['study_end' => '2025-11-30']);

        $this->assertSame('2025-11-30', $certificate->fresh()->study_end->toDateString());
    }
}
