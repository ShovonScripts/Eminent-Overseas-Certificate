<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VerifyCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_certificate_is_shown_without_personal_data(): void
    {
        $certificate = Certificate::factory()->issued()->create([
            'student_name_en' => 'Md. Anik',
            'student_date_of_birth' => '2004-12-09',
            'student_phone' => '+88 01711111111',
            'student_email' => 'anik.private@example.com',
        ]);

        $response = $this->get("/verify/{$certificate->verification_code}");

        $response->assertOk()
            ->assertSee($certificate->certificate_no)
            ->assertSee('Md. Anik')
            ->assertSee('Basic Level')
            ->assertDontSee('2004-12-09')
            ->assertDontSee('+88 01711111111')
            ->assertDontSee('anik.private@example.com');

        $this->assertDatabaseHas('verification_logs', [
            'certificate_id' => $certificate->id,
            'result' => 'valid',
        ]);
    }

    public function test_a_revoked_certificate_shows_a_revoked_banner(): void
    {
        $certificate = Certificate::factory()->revoked()->create();

        $this->get("/verify/{$certificate->verification_code}")
            ->assertOk()
            ->assertSee('revoked', false)
            ->assertDontSee('Valid certificate', false);

        $this->assertDatabaseHas('verification_logs', [
            'certificate_id' => $certificate->id,
            'result' => 'revoked',
        ]);
    }

    public function test_drafts_and_unknown_codes_return_not_found(): void
    {
        $draft = Certificate::factory()->create([
            'verification_code' => strtoupper((string) Str::ulid()),
        ]);

        $this->get("/verify/{$draft->verification_code}")->assertNotFound();
        $this->get('/verify/01ARZ3NDEKTSV4RRFFQ69G5FAV')->assertNotFound();

        $this->assertDatabaseHas('verification_logs', [
            'certificate_id' => $draft->id,
            'result' => 'not_found',
        ]);
        $this->assertDatabaseHas('verification_logs', [
            'certificate_id' => null,
            'code_tried' => '01ARZ3NDEKTSV4RRFFQ69G5FAV',
            'result' => 'not_found',
        ]);
    }

    public function test_lookup_by_certificate_number_redirects_to_public_page(): void
    {
        $certificate = Certificate::factory()->issued()->create();

        $this->post('/verify', ['certificate_no' => $certificate->certificate_no])
            ->assertRedirect(route('verify.show', $certificate->verification_code));
    }

    public function test_unknown_certificate_number_returns_an_error(): void
    {
        $this->from('/verify')
            ->post('/verify', ['certificate_no' => 'EOC-2026-999999'])
            ->assertRedirect('/verify')
            ->assertSessionHasErrors('certificate_no');

        $this->assertDatabaseHas('verification_logs', [
            'code_tried' => 'EOC-2026-999999',
            'result' => 'not_found',
        ]);
    }

    public function test_verify_endpoint_is_rate_limited(): void
    {
        $certificate = Certificate::factory()->issued()->create();
        $url = "/verify/{$certificate->verification_code}";

        for ($i = 0; $i < 30; $i++) {
            $this->get($url)->assertOk();
        }

        $this->get($url)->assertStatus(429);
    }
}
