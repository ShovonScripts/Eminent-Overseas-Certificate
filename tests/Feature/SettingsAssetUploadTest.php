<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsAssetUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_assets_and_dom_pdf_embeds_them(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]));

        $this->get('/admin/settings')
            ->assertOk()
            ->assertSee('Certificate Graphical Assets');

        $response = $this->put('/admin/settings', [
            'school_name' => 'Eminent Overseas & Consultants',
            'school_address' => 'Dhaka, Bangladesh',
            'school_phone' => '+880 1700 000000',
            'school_email' => 'certificates@example.test',
            'verify_base_url' => 'https://example.test/verify',
            'number_prefix' => 'EOC',
            'logo_path' => UploadedFile::fake()->image('logo.png', 400, 300),
            'background_path' => UploadedFile::fake()->image('background.jpg', 2480, 3508),
            'seal_path' => UploadedFile::fake()->image('seal.png', 600, 600),
            'divider_path' => UploadedFile::fake()->image('divider.png', 1400, 20),
        ]);

        $response->assertRedirect('/admin/settings');

        foreach (['logo_path', 'background_path', 'seal_path', 'divider_path'] as $key) {
            $storedPath = Settings::get($key);
            $this->assertStringStartsWith('/storage/images/certificate/', $storedPath);
            Storage::disk('public')->assertExists(substr($storedPath, strlen('/storage/')));
        }

        $certificate = Certificate::factory()->withAttendance()->issued()->create();
        $response = $this->get("/certificates/{$certificate->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $pdf = $response->streamedContent();

        $this->assertGreaterThanOrEqual(4, substr_count($pdf, '/Subtype /Image'));
    }

    public function test_settings_reject_unsupported_graphical_asset_types(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]));

        $this->put('/admin/settings', [
            'school_name' => 'Eminent Overseas & Consultants',
            'school_address' => 'Dhaka, Bangladesh',
            'school_phone' => '+880 1700 000000',
            'school_email' => 'certificates@example.test',
            'verify_base_url' => 'https://example.test/verify',
            'number_prefix' => 'EOC',
            'logo_path' => UploadedFile::fake()->create('logo.webp', 20, 'image/webp'),
        ])->assertSessionHasErrors('logo_path');
    }

    public function test_staff_cannot_view_or_change_official_settings(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_STAFF]));

        $this->get('/admin/settings')->assertForbidden();
        $this->put('/admin/settings', [])->assertForbidden();
    }

    public function test_admin_can_reset_custom_graphical_assets_to_defaults(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]));

        Settings::set('logo_path', '/storage/images/certificate/custom-logo.png');
        Settings::set('background_path', '/storage/images/certificate/custom-bg.png');
        Settings::set('seal_path', '/storage/images/certificate/custom-seal.png');
        Settings::set('divider_path', '/storage/images/certificate/custom-divider.png');

        $this->put('/admin/settings', [
            'school_name' => 'Eminent Overseas & Consultants',
            'school_address' => 'Dhaka, Bangladesh',
            'school_phone' => '+880 1700 000000',
            'school_email' => 'certificates@example.test',
            'verify_base_url' => 'https://example.test/verify',
            'number_prefix' => 'EOC',
            'reset_logo' => '1',
            'reset_background' => '1',
            'reset_seal' => '1',
            'reset_divider' => '1',
        ])->assertRedirect('/admin/settings');

        $this->assertNull(Settings::get('logo_path'));
        $this->assertNull(Settings::get('background_path'));
        $this->assertNull(Settings::get('seal_path'));
        $this->assertNull(Settings::get('divider_path'));
    }

    public function test_settings_cache_is_invalidated_when_values_change(): void
    {
        Settings::set('number_prefix', 'OLD');
        $this->assertSame('OLD', Settings::get('number_prefix'));

        Settings::set('number_prefix', 'NEW');

        $this->assertSame('NEW', Settings::get('number_prefix'));
        $this->assertSame('NEW', Settings::values()['number_prefix']);
    }
}
