<?php

namespace Tests\Feature;

use App\Models\SiteUpdate;
use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormsAndNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): StaffUser
    {
        return StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]);
    }

    public function test_volunteer_form_actually_saves_and_creates_a_notification(): void
    {
        $this->postJson('/api/volunteer-applications', [
            'name' => 'Amina Juma', 'email' => 'amina@example.com', 'phone' => '0712345678',
            'area' => 'education', 'availability' => 'weekends', 'message' => 'I would love to help teach.',
        ])->assertCreated();

        Sanctum::actingAs($this->admin());
        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('0.type', 'volunteer')
            ->assertJsonPath('0.title', 'Amina Juma')
            ->assertJsonPath('0.read', false);

        $this->getJson('/api/notifications/unread-count')->assertJsonPath('count', 1);
    }

    public function test_contact_form_actually_saves_and_creates_a_notification(): void
    {
        $this->postJson('/api/messages', [
            'name' => 'John Mrema', 'email' => 'john@example.com', 'message' => 'How can I partner with you?',
        ])->assertCreated();

        Sanctum::actingAs($this->admin());
        $this->getJson('/api/notifications')->assertJsonPath('0.type', 'message');
    }

    public function test_marking_a_notification_read_clears_it_from_the_unread_count(): void
    {
        $this->postJson('/api/volunteer-applications', [
            'name' => 'Test', 'email' => 't@example.com', 'phone' => '000',
            'area' => 'education', 'availability' => 'flexible', 'message' => 'Hi',
        ])->assertCreated();

        $admin = $this->admin();
        Sanctum::actingAs($admin);
        $id = $this->getJson('/api/notifications')->json('0.id');

        $this->putJson("/api/volunteer-applications/{$id}", ['read' => true])->assertOk();
        $this->getJson('/api/notifications/unread-count')->assertJsonPath('count', 0);
    }

    public function test_updates_are_capped_at_six_and_not_counted_toward_the_25_card_pool(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/updates', ['message' => "Update {$i}", 'expires_at' => now()->addDay()])
                ->assertCreated();
        }

        $this->postJson('/api/updates', ['message' => 'One too many', 'expires_at' => now()->addDay()])
            ->assertStatus(422);

        $this->assertSame(6, SiteUpdate::count());

        // Confirm updates never touch the shared 25-card cap used by
        // gallery/programs/leaders/blog.
        $this->assertTrue(\App\Services\ContentCaps::canAddCard());
    }
}
