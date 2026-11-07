<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_enroll_in_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/admin/security')->assertOk()->assertSee('Not enabled');

        $this->post('/admin/security/enable')->assertRedirect(route('admin.security.show'));

        $secret = (string) $user->fresh()->two_factor_secret;

        $this->assertNotSame('', $secret);

        $this->post('/admin/security/confirm', [
            'code' => app(TwoFactorService::class)->code($secret),
        ])->assertRedirect(route('admin.security.show'));

        $user->refresh();

        $this->assertTrue($user->hasEnabledTwoFactor());
        $this->assertCount(8, $user->twoFactorRecoveryCodes());
    }

    public function test_a_confirmed_user_must_pass_the_challenge_to_sign_in(): void
    {
        $user = $this->userWithTwoFactor();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->assertGuest();
        $this->get('/two-factor-challenge')->assertOk();

        $secret = (string) $user->fresh()->two_factor_secret;

        $this->post('/two-factor-challenge', [
            'code' => app(TwoFactorService::class)->code($secret),
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_recovery_code_can_be_used_once(): void
    {
        $user = $this->userWithTwoFactor();
        $recoveryCode = $user->twoFactorRecoveryCodes()[0];

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->post('/two-factor-challenge', [
            'recovery_code' => $recoveryCode,
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->assertNotContains($recoveryCode, $user->fresh()->twoFactorRecoveryCodes());
    }

    public function test_an_invalid_code_is_rejected(): void
    {
        $user = $this->userWithTwoFactor();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->from('/two-factor-challenge')
            ->post('/two-factor-challenge', ['code' => '000000'])
            ->assertRedirect('/two-factor-challenge')
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_two_factor_can_be_disabled_with_the_current_password(): void
    {
        $user = $this->userWithTwoFactor(['password' => Hash::make('password')]);

        $this->actingAs($user)
            ->post('/admin/security/disable', ['password' => 'password'])
            ->assertRedirect(route('admin.security.show'));

        $user->refresh();

        $this->assertFalse($user->hasEnabledTwoFactor());
        $this->assertNull($user->two_factor_secret);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function userWithTwoFactor(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $twoFactor = app(TwoFactorService::class);

        $user->forceFill([
            'two_factor_secret' => $twoFactor->generateSecret(),
            'two_factor_recovery_codes' => $twoFactor->recoveryCodes(),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $user->refresh();
    }
}
