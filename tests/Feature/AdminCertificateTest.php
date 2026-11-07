<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_and_update_a_draft_certificate(): void
    {
        Storage::fake('local');

        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $course = Course::factory()->create();
        $instructors = Instructor::factory()->count(2)->create();

        $payload = $this->payload($course, $instructors);

        $this->actingAs($staff)
            ->post('/admin/certificates', $payload)
            ->assertRedirect();

        $certificate = Certificate::firstOrFail();

        $this->assertSame(CertificateStatus::Draft, $certificate->status);
        $this->assertSame('Md. Anik Rahman', $certificate->student_name_en);
        $this->assertCount(2, $certificate->attendances);
        $this->assertCount(2, $certificate->instructors);
        $this->assertSame(33, $certificate->attendances->first()->class_hours);

        $updated = $payload;
        $updated['national_holidays'] = '10月1日; 12月25日';
        $updated['attendances'][0]['attendance_hours'] = 30;

        $this->actingAs($staff)
            ->put("/admin/certificates/{$certificate->id}", $updated)
            ->assertRedirect(route('admin.certificates.show', $certificate));

        $certificate->refresh();

        $this->assertSame('10月1日; 12月25日', $certificate->national_holidays);
        $this->assertSame(30, $certificate->attendances->first()->attendance_hours);
    }

    public function test_attendance_must_not_exceed_class_hours(): void
    {
        Storage::fake('local');

        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $course = Course::factory()->create();
        $instructors = Instructor::factory()->count(2)->create();

        $payload = $this->payload($course, $instructors);
        $payload['attendances'][0]['attendance_hours'] = 99;

        $this->actingAs($staff)
            ->post('/admin/certificates', $payload)
            ->assertSessionHasErrors('attendances.0.attendance_hours');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_suggest_returns_a_row_per_month(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($staff)
            ->postJson('/admin/certificates/suggest', [
                'study_start' => '2025-06-02',
                'study_end' => '2025-08-31',
                'class_days' => ['Saturday'],
                'hours_per_day' => 3,
                'national_holidays' => '2025-06-14',
            ])
            ->assertOk()
            ->assertJsonCount(3, 'rows')
            ->assertJsonPath('rows.0.month', 6)
            ->assertJsonPath('rows.0.class_hours', 9);
    }

    public function test_a_draft_can_be_issued_then_revoked(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $course = Course::factory()->create();
        $instructors = Instructor::factory()->count(2)->create();

        $this->actingAs($admin)
            ->post('/admin/certificates', $this->payload($course, $instructors));

        $certificate = Certificate::firstOrFail();

        $this->actingAs($admin)
            ->post("/admin/certificates/{$certificate->id}/issue")
            ->assertRedirect(route('admin.certificates.show', $certificate));

        $certificate->refresh();

        $this->assertTrue($certificate->isIssued());
        $this->assertNotNull($certificate->certificate_no);
        $this->assertNotNull($certificate->verification_code);
        $this->assertNotNull($certificate->pdf_path);
        $this->assertSame($admin->id, $certificate->issued_by);

        $this->get("/verify/{$certificate->verification_code}")->assertOk();

        $this->actingAs($admin)
            ->post("/admin/certificates/{$certificate->id}/revoke", [
                'revoke_reason' => 'Wrong student name',
            ])
            ->assertRedirect(route('admin.certificates.show', $certificate));

        $this->assertTrue($certificate->fresh()->isRevoked());
        $this->assertDatabaseHas('certificates', [
            'id' => $certificate->id,
            'status' => CertificateStatus::Revoked->value,
            'revoke_reason' => 'Wrong student name',
        ]);
    }

    public function test_course_and_instructor_can_be_managed(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($staff)->post('/admin/courses', [
            'name_en' => 'Basic Level',
            'name_ja' => '基礎コース',
            'level_code' => 'N5',
            'aim_level_en' => 'JLPT N5',
            'aim_level_ja' => 'JLPT N5',
            'total_hours' => 180,
            'book_en' => 'Minna no Nihongo',
            'book_ja' => 'みんなの日本語',
            'learning_context' => [
                ['ja' => '日本で働く', 'en' => 'Work in Japan'],
            ],
            'default_hours_per_day' => 3,
            'default_days_per_week' => 3,
        ])->assertRedirect();

        $this->assertDatabaseHas('courses', ['name_en' => 'Basic Level', 'level_code' => 'N5']);

        $this->actingAs($staff)->post('/admin/instructors', [
            'name' => 'Taro Yamada',
            'title' => '講師 / Instructor',
            'email' => 'taro@example.com',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('instructors', ['name' => 'Taro Yamada', 'is_active' => 1]);
    }

    /**
     * @param  Collection<int, Instructor>  $instructors
     * @return array<string, mixed>
     */
    private function payload(Course $course, $instructors): array
    {
        return [
            'student_name_en' => 'Md. Anik Rahman',
            'student_name_ja' => 'アニク・ラーマン',
            'student_date_of_birth' => '2004-12-09',
            'student_gender' => 'male',
            'student_email' => 'anik@example.com',
            'student_phone' => '+8801711111111',
            'course_id' => $course->id,
            'study_start' => '2025-06-02',
            'study_end' => '2025-10-27',
            'hours_per_day' => 3,
            'days_per_week' => 3,
            'class_days' => ['Saturday', 'Monday', 'Wednesday'],
            'national_holidays' => '6月9日; 6月11日',
            'issued_on' => '2026-01-15',
            'instructors' => $instructors->pluck('id')->all(),
            'attendances' => [
                ['year' => 2025, 'month' => 6, 'class_hours' => 33, 'attendance_hours' => 33],
                ['year' => 2025, 'month' => 7, 'class_hours' => 39, 'attendance_hours' => 36],
            ],
        ];
    }
}
