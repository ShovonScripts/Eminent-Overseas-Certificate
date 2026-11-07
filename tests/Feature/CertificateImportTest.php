<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_csv_issues_certificates_and_reports_bad_rows(): void
    {
        Storage::fake('local');

        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $course = Course::factory()->create(['level_code' => 'N5', 'name_en' => 'Basic Level']);
        $first = Instructor::factory()->create(['name' => 'Taro Yamada', 'is_active' => true]);
        $second = Instructor::factory()->create(['name' => 'Hanako Suzuki', 'is_active' => true]);

        $csv = implode("\n", [
            'student_name_en,student_name_ja,student_date_of_birth,student_gender,student_email,student_phone,course,study_start,study_end,issued_on,class_days,hours_per_day,national_holidays,instructors,2025-06,2025-07,2025-08',
            'Md. Anik Rahman,アニク・ラーマン,2004-12-09,male,anik@example.com,+8801711111111,N5,2025-06-02,2025-08-31,2025-09-15,Saturday;Monday;Wednesday,3,6月9日,Taro Yamada;Hanako Suzuki,33,36,30',
            'Non Existent,Non Existent,2004-12-09,male,,+8801711111111,N5,2025-06-02,2025-08-31,2025-09-15,Saturday,3,,Non Existent Instructor,9,12,12',
        ]);

        $this->actingAs($staff)
            ->post('/admin/certificates/import', [
                'csv' => UploadedFile::fake()->createWithContent('certificates.csv', $csv),
            ])
            ->assertOk()
            ->assertSee('1 certificate issued successfully')
            ->assertSee('Non Existent')
            ->assertSee('was not found');

        $certificate = Certificate::firstOrFail();

        $this->assertTrue($certificate->isIssued());
        $this->assertSame(3, $certificate->attendances()->count());
        $this->assertSame(99, $certificate->total_attendance_hours);
        $this->assertSame([$first->id, $second->id], $certificate->instructors->pluck('id')->all());
        $this->assertNotNull($certificate->pdf_path);
        Storage::disk('local')->assertExists($certificate->pdf_path);
    }

    public function test_the_template_can_be_downloaded(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($staff)
            ->get('/admin/certificates/import/template')
            ->assertOk()
            ->assertDownload('certificate-import-template.csv');
    }

    public function test_rows_with_attendance_above_class_hours_are_rejected(): void
    {
        Storage::fake('local');

        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        Course::factory()->create(['level_code' => 'N5']);
        Instructor::factory()->create(['name' => 'Taro Yamada', 'is_active' => true]);

        $csv = implode("\n", [
            'student_name_en,student_date_of_birth,student_gender,course,study_start,study_end,issued_on,class_days,hours_per_day,instructors,2025-06',
            'Test Student,2004-12-09,male,N5,2025-06-02,2025-06-30,2025-07-15,Saturday,3,Taro Yamada,999',
        ]);

        $this->actingAs($staff)
            ->post('/admin/certificates/import', [
                'csv' => UploadedFile::fake()->createWithContent('certificates.csv', $csv),
            ])
            ->assertOk()
            ->assertSee('exceeds the calculated class hours');

        $this->assertDatabaseCount('certificates', 0);
    }
}
