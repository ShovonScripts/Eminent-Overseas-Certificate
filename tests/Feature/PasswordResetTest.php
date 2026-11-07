<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_reset_link_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_a_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->get("/reset-password/{$token}")->assertOk()->assertSee('New password');

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'N3w-Password!2026',
            'password_confirmation' => 'N3w-Password!2026',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('N3w-Password!2026', $user->fresh()->password));
    }

    public function test_an_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'N3w-Password!2026',
            'password_confirmation' => 'N3w-Password!2026',
        ])->assertSessionHasErrors('email');
    }
}
