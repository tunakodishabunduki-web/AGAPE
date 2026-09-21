<?php

namespace Tests\Feature;

use App\Models\ContactInfo;
use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactInfoAndStaffRolesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): StaffUser
    {
        return StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]);
    }

    public function test_admin_can_update_contact_info_and_it_appears_in_the_public_feed(): void
    {
        Sanctum::actingAs($this->admin());

        $this->putJson('/api/contact-info', [
            'email' => 'help@agapefamilyfoundation.org',
            'phone' => '+255 22 999 0000',
            'office' => 'New Office Address, Dar es Salaam',
        ])->assertOk()->assertJsonPath('email', 'help@agapefamilyfoundation.org');

        // The Contact page's cards read this from the public feed, not the
        // admin-only endpoint — confirming it round-trips through both matters.
        $public = $this->getJson('/api/content')->json('contact');
        $this->assertSame('help@agapefamilyfoundation.org', $public['email']);
        $this->assertSame('New Office Address, Dar es Salaam', $public['office']);
    }

    public function test_editor_can_be_promoted_to_admin(): void
    {
        $admin = $this->admin();
        $editor = StaffUser::create([
            'username' => 'editor1', 'password' => bcrypt('irrelevant'),
            'role' => 'editor', 'must_change_password' => false,
        ]);

        Sanctum::actingAs($admin);
        $this->putJson("/api/staff/{$editor->id}/role", ['role' => 'admin'])
            ->assertOk()->assertJsonPath('role', 'admin');

        $this->assertSame('admin', $editor->fresh()->role);
    }

    public function test_the_last_remaining_admin_cannot_be_demoted(): void
    {
        $onlyAdmin = $this->admin();
        Sanctum::actingAs($onlyAdmin);

        // Can't demote self via the role endpoint any more than any other
        // account could be the sole remaining admin and get demoted.
        $this->putJson("/api/staff/{$onlyAdmin->id}/role", ['role' => 'editor'])
            ->assertStatus(422);

        $this->assertSame('admin', $onlyAdmin->fresh()->role);
    }

    public function test_demoting_one_of_two_admins_is_allowed(): void
    {
        $admin1 = $this->admin();
        $admin2 = StaffUser::create([
            'username' => 'admin2', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]);

        Sanctum::actingAs($admin1);
        $this->putJson("/api/staff/{$admin2->id}/role", ['role' => 'editor'])->assertOk();

        $this->assertSame('editor', $admin2->fresh()->role);
    }

    public function test_editor_cannot_change_anyones_role(): void
    {
        $editor = StaffUser::create([
            'username' => 'editor1', 'password' => bcrypt('irrelevant'),
            'role' => 'editor', 'must_change_password' => false,
        ]);
        $other = StaffUser::create([
            'username' => 'editor2', 'password' => bcrypt('irrelevant'),
            'role' => 'editor', 'must_change_password' => false,
        ]);

        Sanctum::actingAs($editor);
        $this->putJson("/api/staff/{$other->id}/role", ['role' => 'admin'])->assertStatus(403);
    }
}
