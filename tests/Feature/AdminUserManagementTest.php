<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_manage_staff_accounts(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($admin);

        $this->get('/admin/users')->assertOk()->assertSee('Staff accounts');
        $this->get('/admin/users/create')->assertOk();

        $this->post('/admin/users', [
            'name' => 'New Staff',
            'email' => 'staff@example.com',
            'role' => User::ROLE_STAFF,
            'password' => 'Str0ng-Password!2026',
            'password_confirmation' => 'Str0ng-Password!2026',
        ])->assertRedirect(route('admin.users.index'));

        $staff = User::where('email', 'staff@example.com')->firstOrFail();

        $this->assertSame(User::ROLE_STAFF, $staff->role);
        $this->assertTrue(Hash::check('Str0ng-Password!2026', $staff->password));

        $this->put("/admin/users/{$staff->id}", [
            'name' => 'Renamed Staff',
            'email' => 'staff@example.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('Renamed Staff', $staff->fresh()->name);
        $this->assertSame(User::ROLE_SUPER_ADMIN, $staff->fresh()->role);

        $this->delete("/admin/users/{$staff->id}")->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($staff);

        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/users/create')->assertForbidden();
        $this->post('/admin/users', [])->assertForbidden();
    }

    public function test_the_last_super_admin_cannot_be_demoted_or_deleted(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($admin);

        $this->from("/admin/users/{$admin->id}/edit")
            ->put("/admin/users/{$admin->id}", [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => User::ROLE_STAFF,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect("/admin/users/{$admin->id}/edit")
            ->assertSessionHasErrors('role');

        $this->assertSame(User::ROLE_SUPER_ADMIN, $admin->fresh()->role);

        $this->delete("/admin/users/{$admin->id}")->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_weak_passwords_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Weak User',
                'email' => 'weak@example.com',
                'role' => User::ROLE_STAFF,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'weak@example.com']);
    }
}
