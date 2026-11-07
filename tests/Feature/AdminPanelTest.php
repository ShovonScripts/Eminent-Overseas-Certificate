<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_browse_all_resources(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->actingAs($admin);

        $course = Course::factory()->create();
        $instructor = Instructor::factory()->create();
        $certificate = Certificate::factory()->issued()->withAttendance()->withInstructors()->create();
        $draft = Certificate::factory()->create();

        $this->get('/admin')->assertOk();

        foreach (['courses', 'instructors', 'certificates', 'verification-logs'] as $resource) {
            $this->get("/admin/{$resource}")->assertOk();
        }

        $this->get('/admin/courses/create')->assertOk();
        $this->get('/admin/instructors/create')->assertOk();
        $this->get('/admin/certificates/create')->assertOk();

        $this->get("/admin/courses/{$course->id}")->assertOk();
        $this->get("/admin/courses/{$course->id}/edit")->assertOk();
        $this->get("/admin/instructors/{$instructor->id}/edit")->assertOk();
        $this->get("/admin/certificates/{$certificate->id}")->assertOk();
        $this->get("/admin/certificates/{$draft->id}/edit")->assertOk();
        $this->get("/admin/certificates/{$certificate->id}/edit")->assertForbidden();
    }

    public function test_staff_cannot_delete_or_revoke(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $issued = Certificate::factory()->issued()->create();
        $draft = Certificate::factory()->create();

        $this->actingAs($staff);

        $this->delete("/admin/certificates/{$draft->id}")->assertForbidden();
        $this->post("/admin/certificates/{$issued->id}/revoke", ['revoke_reason' => 'Mistake'])->assertForbidden();
    }
}
