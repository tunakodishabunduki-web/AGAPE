<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pageview_events_can_be_recorded_without_authentication(): void
    {
        $this->postJson('/api/analytics', ['type' => 'pageview', 'page' => '/'])
            ->assertCreated();

        $this->assertSame(1, AnalyticsEvent::count());
    }

    public function test_summary_zero_fills_days_with_no_visits(): void
    {
        Sanctum::actingAs(StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]));

        AnalyticsEvent::create(['type' => 'pageview', 'page' => '/', 'created_at' => now()]);

        $response = $this->getJson('/api/analytics/summary?days=7')->assertOk();
        $series = $response->json('series');

        // 7 days requested, 7 days back regardless of how many actually had visits —
        // a quiet day should show as an honest 0, not simply be missing from the chart.
        $this->assertCount(7, $series);
        $this->assertSame(1, $response->json('total'));
    }

    public function test_summary_only_counts_pageview_events_not_other_action_types(): void
    {
        Sanctum::actingAs(StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]));

        AnalyticsEvent::create(['type' => 'pageview', 'created_at' => now()]);
        AnalyticsEvent::create(['type' => 'donation_click', 'created_at' => now()]);

        $response = $this->getJson('/api/analytics/summary?days=7');
        $this->assertSame(1, $response->json('total'));
    }

    public function test_summary_is_admin_only(): void
    {
        $this->getJson('/api/analytics/summary')->assertUnauthorized();
    }

    public function test_days_parameter_is_clamped_to_a_sane_range(): void
    {
        Sanctum::actingAs(StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]));

        // Requesting an absurd range shouldn't be able to force an enormous query.
        $response = $this->getJson('/api/analytics/summary?days=99999')->assertOk();
        $this->assertLessThanOrEqual(90, $response->json('days'));
    }
}
