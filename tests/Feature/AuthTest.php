<?php

namespace Tests\Feature;

use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_fails_with_wrong_password(): void
    {
        StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('correct-password'),
            'role' => 'admin', 'must_change_password' => false,
        ]);

        $this->postJson('/api/login', ['username' => 'admin', 'password' => 'wrong-password'])
            ->assertStatus(401);
    }

    public function test_login_succeeds_with_correct_password_and_no_2fa(): void
    {
        StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('correct-password'),
            'role' => 'admin', 'must_change_password' => false,
        ]);

        $this->postJson('/api/login', ['username' => 'admin', 'password' => 'correct-password'])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('mustChangePassword', false);
    }

    public function test_login_with_2fa_enabled_requires_a_code(): void
    {
        StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('correct-password'),
            'role' => 'admin', 'must_change_password' => false,
            'totp_enabled' => true, 'totp_secret' => 'ABCDEFGHIJKLMNOP',
        ]);

        // Correct password, no code yet — should ask for 2FA, not log in.
        $this->postJson('/api/login', ['username' => 'admin', 'password' => 'correct-password'])
            ->assertStatus(401)
            ->assertJsonPath('requires2fa', true);
    }

    public function test_a_staff_member_with_a_temporary_password_is_blocked_from_other_actions(): void
    {
        $staff = StaffUser::create([
            'username' => 'newstaff', 'password' => bcrypt('temp-password'),
            'role' => 'editor', 'must_change_password' => true,
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($staff);

        // Blocked: hasn't changed their temporary password yet.
        $this->getJson('/api/hero')->assertStatus(403)->assertJsonPath('mustChangePassword', true);

        // Allowed: the one route that's always open, so they CAN escape the trap.
        $this->postJson('/api/password/change', [
            'current_password' => 'temp-password', 'new_password' => 'a-real-new-password-123',
        ])->assertOk();
    }
}
