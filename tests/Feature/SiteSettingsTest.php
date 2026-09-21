<?php

namespace Tests\Feature;

use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): StaffUser
    {
        return StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]);
    }

    public function test_defaults_are_created_on_first_read(): void
    {
        Sanctum::actingAs($this->admin());
        $this->getJson('/api/site-settings')
            ->assertOk()
            ->assertJsonPath('org_name', 'Agape Family Foundation');
    }

    public function test_admin_can_update_seo_and_social_fields(): void
    {
        Sanctum::actingAs($this->admin());

        $this->putJson('/api/site-settings', [
            'org_name' => 'Agape Family Foundation',
            'seo_title' => 'Custom SEO Title',
            'seo_description' => 'Custom description for search results.',
            'social_facebook' => 'https://facebook.com/agapefamilyfoundation',
        ])->assertOk()
            ->assertJsonPath('seo_title', 'Custom SEO Title')
            ->assertJsonPath('social_facebook', 'https://facebook.com/agapefamilyfoundation');

        // Confirms this actually persisted, not just echoed back in the response.
        $this->assertSame('Custom SEO Title', \App\Models\SiteSetting::current()->seo_title);
    }

    public function test_invalid_social_url_is_rejected(): void
    {
        Sanctum::actingAs($this->admin());
        $this->putJson('/api/site-settings', [
            'org_name' => 'Agape Family Foundation',
            'social_twitter' => 'not-a-valid-url',
        ])->assertStatus(422);
    }

    public function test_a_null_social_field_clears_a_previously_set_link(): void
    {
        Sanctum::actingAs($this->admin());
        $this->putJson('/api/site-settings', [
            'org_name' => 'Agape Family Foundation',
            'social_facebook' => 'https://facebook.com/agape',
        ])->assertOk();

        $this->putJson('/api/site-settings', [
            'org_name' => 'Agape Family Foundation',
            'social_facebook' => null,
        ])->assertOk()->assertJsonPath('social_facebook', null);
    }

    public function test_guests_cannot_change_site_settings(): void
    {
        $this->putJson('/api/site-settings', ['org_name' => 'Hijacked'])->assertUnauthorized();
    }

    public function test_seo_settings_actually_appear_in_the_rendered_homepage_head(): void
    {
        Sanctum::actingAs($this->admin());
        $this->putJson('/api/site-settings', [
            'org_name' => 'Agape Family Foundation',
            'seo_title' => 'Unique Searchable Title For Testing',
        ])->assertOk();

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('Unique Searchable Title For Testing', $html);
    }
}
