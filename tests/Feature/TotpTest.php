<?php

namespace Tests\Feature;

use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TotpTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): StaffUser
    {
        return StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]);
    }

    public function test_setup_stores_a_pending_secret_without_enabling_2fa_yet(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/totp/setup')->assertOk();
        $this->assertNotEmpty($response->json('secret'));

        $admin->refresh();
        $this->assertNotNull($admin->totp_pending_secret);
        $this->assertFalse($admin->totp_enabled); // not active until confirmed with a real code
    }

    public function test_confirming_with_the_right_code_enables_2fa(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        $secret = $this->postJson('/api/admin/totp/setup')->json('secret');
        $validCode = (new Google2FA())->getCurrentOtp($secret);

        $this->postJson('/api/admin/totp/confirm', ['code' => $validCode])->assertOk();

        $admin->refresh();
        $this->assertTrue($admin->totp_enabled);
        $this->assertNull($admin->totp_pending_secret); // pending secret is consumed, not left lying around
    }

    public function test_confirming_with_the_wrong_code_does_not_enable_2fa(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/totp/setup');
        $this->postJson('/api/admin/totp/confirm', ['code' => '000000'])->assertStatus(422);

        $this->assertFalse($admin->fresh()->totp_enabled);
    }

    public function test_login_requires_the_2fa_code_once_enabled_and_accepts_a_valid_one(): void
    {
        $admin = StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('correct-password'),
            'role' => 'admin', 'must_change_password' => false,
        ]);
        $secret = (new Google2FA())->generateSecretKey();
        $admin->forceFill(['totp_secret' => $secret, 'totp_enabled' => true])->save();

        // Right password, no code yet.
        $this->postJson('/api/login', ['username' => 'admin', 'password' => 'correct-password'])
            ->assertStatus(401)->assertJsonPath('requires2fa', true);

        // Right password, right code.
        $validCode = (new Google2FA())->getCurrentOtp($secret);
        $this->postJson('/api/login', ['username' => 'admin', 'password' => 'correct-password', 'code' => $validCode])
            ->assertOk()->assertJsonPath('ok', true);
    }

    public function test_disabling_2fa_requires_a_valid_current_code(): void
    {
        $admin = $this->admin();
        $secret = (new Google2FA())->generateSecretKey();
        $admin->forceFill(['totp_secret' => $secret, 'totp_enabled' => true])->save();
        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/totp/disable', ['code' => '000000'])->assertStatus(422);
        $this->assertTrue($admin->fresh()->totp_enabled);

        $validCode = (new Google2FA())->getCurrentOtp($secret);
        $this->postJson('/api/admin/totp/disable', ['code' => $validCode])->assertOk();
        $this->assertFalse($admin->fresh()->totp_enabled);
    }
}
