<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): StaffUser
    {
        return StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]);
    }

    public function test_first_network_added_becomes_primary_automatically(): void
    {
        Sanctum::actingAs($this->admin());

        $this->postJson('/api/payment-methods', ['network' => 'mpesa', 'lipa_number' => '123456'])
            ->assertCreated()
            ->assertJsonPath('is_primary', true);
    }

    public function test_a_third_network_is_rejected(): void
    {
        Sanctum::actingAs($this->admin());
        PaymentMethod::create(['network' => 'mpesa', 'lipa_number' => '1', 'is_primary' => true]);
        PaymentMethod::create(['network' => 'tigopesa', 'lipa_number' => '2', 'is_primary' => false]);

        $this->postJson('/api/payment-methods', ['network' => 'airtel', 'lipa_number' => '3'])
            ->assertStatus(422);

        $this->assertSame(2, PaymentMethod::count());
    }

    public function test_the_same_network_cannot_be_added_twice(): void
    {
        Sanctum::actingAs($this->admin());
        PaymentMethod::create(['network' => 'mpesa', 'lipa_number' => '1', 'is_primary' => true]);

        $this->postJson('/api/payment-methods', ['network' => 'mpesa', 'lipa_number' => '999'])
            ->assertStatus(422);
    }

    public function test_making_a_network_primary_unsets_the_other_one(): void
    {
        Sanctum::actingAs($this->admin());
        $mpesa = PaymentMethod::create(['network' => 'mpesa', 'lipa_number' => '1', 'is_primary' => true]);
        $tigo = PaymentMethod::create(['network' => 'tigopesa', 'lipa_number' => '2', 'is_primary' => false]);

        $this->putJson("/api/payment-methods/{$tigo->id}", ['is_primary' => true])
            ->assertOk();

        $this->assertFalse($mpesa->fresh()->is_primary);
        $this->assertTrue($tigo->fresh()->is_primary);
    }

    public function test_deleting_the_primary_network_promotes_the_remaining_one(): void
    {
        Sanctum::actingAs($this->admin());
        $mpesa = PaymentMethod::create(['network' => 'mpesa', 'lipa_number' => '1', 'is_primary' => true]);
        $tigo = PaymentMethod::create(['network' => 'tigopesa', 'lipa_number' => '2', 'is_primary' => false]);

        $this->deleteJson("/api/payment-methods/{$mpesa->id}")->assertOk();

        $this->assertTrue($tigo->fresh()->is_primary);
    }

    public function test_guests_cannot_manage_payment_methods(): void
    {
        $this->postJson('/api/payment-methods', ['network' => 'mpesa', 'lipa_number' => '1'])
            ->assertUnauthorized();
    }
}
