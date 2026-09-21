<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\SiteSetting;
use App\Models\StaffUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SeoAndSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_includes_organization_schema_markup(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('application/ld+json', $html);
        $this->assertStringContainsString('"@type":"NGO"', $html);
        $this->assertStringContainsString('Agape Family Foundation', $html);
    }

    public function test_blog_post_includes_article_schema_markup(): void
    {
        $post = BlogPost::create([
            'title' => 'A Real Post', 'slug' => 'a-real-post', 'excerpt' => 'An excerpt.',
            'body' => 'Body text.', 'published_at' => now(),
        ]);

        $html = $this->get('/pages/blog-post.php?id=a-real-post')->getContent();

        $this->assertStringContainsString('"@type":"Article"', $html);
        $this->assertStringContainsString('A Real Post', $html);
    }

    public function test_search_console_verification_meta_tag_only_appears_when_set(): void
    {
        // Not set — tag should not appear at all, not appear empty.
        $html = $this->get('/')->getContent();
        $this->assertStringNotContainsString('google-site-verification', $html);

        SiteSetting::current()->update(['search_console_verification' => 'abc123verificationcode']);

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('google-site-verification', $html);
        $this->assertStringContainsString('abc123verificationcode', $html);
    }

    public function test_sitemap_is_valid_xml_and_includes_published_posts(): void
    {
        BlogPost::create([
            'title' => 'Sitemap Test Post', 'slug' => 'sitemap-test-post', 'excerpt' => 'x',
            'body' => 'x', 'published_at' => now(),
        ]);
        // An unpublished post should NOT appear — it isn't a public URL yet.
        BlogPost::create([
            'title' => 'Draft Post', 'slug' => 'draft-post', 'excerpt' => 'x', 'body' => 'x', 'published_at' => null,
        ]);

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'sitemap.xml must be valid, parseable XML.');

        $this->assertStringContainsString('sitemap-test-post', $response->getContent());
        $this->assertStringNotContainsString('draft-post', $response->getContent());
    }

    public function test_new_blog_post_slugs_are_clean_not_suffixed_with_a_random_id(): void
    {
        Sanctum::actingAs(StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]));

        $response = $this->postJson('/api/blog', [
            'title' => 'New Scholarship Program Launched',
            'excerpt' => 'x', 'body' => 'x',
        ])->assertCreated();

        $this->assertSame('new-scholarship-program-launched', $response->json('slug'));
    }

    public function test_a_second_post_with_the_same_title_gets_a_numbered_slug_not_a_collision(): void
    {
        Sanctum::actingAs(StaffUser::create([
            'username' => 'admin', 'password' => bcrypt('irrelevant'),
            'role' => 'admin', 'must_change_password' => false,
        ]));

        $this->postJson('/api/blog', ['title' => 'Same Title', 'excerpt' => 'x', 'body' => 'x'])->assertCreated();
        $second = $this->postJson('/api/blog', ['title' => 'Same Title', 'excerpt' => 'x', 'body' => 'x'])->assertCreated();

        $this->assertSame('same-title-2', $second->json('slug'));
    }
}
