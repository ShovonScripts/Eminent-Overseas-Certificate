<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $certificate = Certificate::factory()->issued()->create();

        $this->get("/certificates/{$certificate->id}/pdf")
            ->assertRedirect(route('login'));
    }

    public function test_staff_can_download_an_issued_certificate(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $certificate = Certificate::factory()->issued()->withAttendance()->withInstructors()->create();

        $response = $this->actingAs($staff)
            ->get("/certificates/{$certificate->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->streamedContent());
    }

    public function test_staff_can_preview_a_draft_certificate(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $certificate = Certificate::factory()->withAttendance()->withInstructors()->create();

        $response = $this->actingAs($staff)
            ->get("/certificates/{$certificate->id}/preview")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->streamedContent());
    }

    public function test_a_draft_certificate_cannot_be_downloaded(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $certificate = Certificate::factory()->create();

        $this->actingAs($staff)
            ->get("/certificates/{$certificate->id}/pdf")
            ->assertForbidden();
    }

    public function test_only_a_super_admin_can_revoke_or_delete(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $issued = Certificate::factory()->issued()->create();
        $draft = Certificate::factory()->create();

        $this->assertFalse($staff->can('revoke', $issued));
        $this->assertTrue($admin->can('revoke', $issued));

        $this->assertFalse($staff->can('delete', $draft));
        $this->assertTrue($admin->can('delete', $draft));

        $this->assertFalse($admin->can('delete', $issued));
    }
}
