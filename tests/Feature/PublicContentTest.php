<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_endpoint_is_reachable_without_authentication(): void
    {
        $this->getJson('/api/content')->assertOk();
    }

    public function test_content_endpoint_includes_every_section_the_frontend_reads(): void
    {
        $response = $this->getJson('/api/content');

        // These exact keys are what assets/js/site-data.js indexes into on every
        // public page — losing one of these silently breaks a page, not a 500.
        $response->assertJsonStructure([
            'hero' => ['headline', 'description', 'image'],
            'programs', 'leaders', 'gallery', 'reviews', 'blog', 'updates',
            'whatsappContacts',
            'contact' => ['email', 'whatsapp', 'phone', 'office', 'regionalOffices', 'availability', 'intro'],
            'donation' => ['lipaNamba', 'primaryNetwork'],
        ]);
    }

    public function test_gallery_items_appear_in_the_public_feed_in_the_shape_the_frontend_expects(): void
    {
        GalleryItem::create(['image_path' => 'uploads/a.jpg', 'title' => 'Sports Day', 'caption' => 'Annual sports day', 'sort_order' => 0]);

        $response = $this->getJson('/api/content');

        // Tuple order matters: the frontend reads gallery[i][0]/[1]/[2] positionally.
        $response->assertJsonPath('gallery.0.0', 'uploads/a.jpg');
        $response->assertJsonPath('gallery.0.1', 'Sports Day');
        $response->assertJsonPath('gallery.0.2', 'Annual sports day');
    }

    public function test_primary_payment_network_is_reported_correctly_to_the_public_feed(): void
    {
        PaymentMethod::create(['network' => 'mpesa', 'lipa_number' => '123456', 'account_name' => 'Agape', 'is_primary' => true]);
        PaymentMethod::create(['network' => 'tigopesa', 'lipa_number' => '654321', 'is_primary' => false]);

        $response = $this->getJson('/api/content');

        $response->assertJsonPath('donation.primaryNetwork', 'mpesa');
        $response->assertJsonPath('donation.lipaNamba.mpesa.number', '123456');
        $response->assertJsonPath('donation.lipaNamba.tigopesa.number', '654321');
    }

    public function test_unconfigured_networks_do_not_appear_in_the_public_feed(): void
    {
        // Only mpesa configured — this is what makes "clients only see Mpesa on
        // top" true: tigopesa/airtel/halotel simply aren't present as keys at all.
        PaymentMethod::create(['network' => 'mpesa', 'lipa_number' => '123456', 'is_primary' => true]);

        $response = $this->getJson('/api/content');

        $response->assertJsonMissingPath('donation.lipaNamba.tigopesa');
        $response->assertJsonMissingPath('donation.lipaNamba.airtel');
        $response->assertJsonMissingPath('donation.lipaNamba.halotel');
    }

    public function test_homepage_renders_full_storage_urls_not_bare_relative_paths(): void
    {
        // Regression test for a real bug: image_path values are stored as bare
        // relative paths ("uploads/xyz.jpg"). Used bare in rendered HTML, a
        // browser resolves that against the CURRENT page's URL, not the site
        // root — every image on the live site was broken until this was fixed.
        GalleryItem::create(['image_path' => 'uploads/sports-day.jpg', 'title' => 'Sports Day', 'sort_order' => 0]);

        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('url(\'uploads/sports-day.jpg\')', $html);
        $this->assertStringContainsString('/storage/uploads/sports-day.jpg', $html);
    }

    public function test_homepage_actually_renders_program_and_hero_content_server_side(): void
    {
        // Regression test for a deeper bug: the marker comments this controller
        // was written against (MARKER:PROGRAMS etc.) never actually matched
        // anything in the real template — it used a different, incompatible
        // "SSR:NAME:START/END" convention. Every injectMarker() call was a
        // silent no-op; the site only ever worked via client-side JS, meaning
        // a crawler that doesn't execute JavaScript saw none of this content.
        \App\Models\Program::create([
            'title' => 'Education Access', 'description' => 'Uniforms, books, and tuition.',
            'stat_number' => '312', 'stat_label' => 'children in school', 'sort_order' => 0,
        ]);
        \App\Models\HeroContent::current()->update(['stat_value' => '777+', 'headline' => 'A Real Headline']);

        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('Education Access', $html);
        $this->assertStringContainsString('312', $html);
        $this->assertStringContainsString('777+', $html);
        $this->assertStringContainsString('A Real Headline', $html);
    }

    public function test_gallery_leadership_and_blog_pages_render_real_data_server_side(): void
    {
        // Same class of bug as above, found on three more pages while fixing it.
        GalleryItem::create(['image_path' => 'uploads/a.jpg', 'title' => 'Unique Gallery Title', 'sort_order' => 0]);
        \App\Models\Leader::create(['name' => 'Unique Leader Name', 'title' => 'Director', 'bio' => 'Bio text.', 'sort_order' => 0]);
        \App\Models\BlogPost::create([
            'title' => 'Unique Blog Title', 'slug' => 'unique-blog-title', 'excerpt' => 'Excerpt.',
            'body' => 'Body.', 'published_at' => now(),
        ]);

        $this->assertStringContainsString('Unique Gallery Title', $this->get('/pages/gallery.php')->getContent());
        $this->assertStringContainsString('Unique Leader Name', $this->get('/pages/leadership.php')->getContent());
        $this->assertStringContainsString('Unique Blog Title', $this->get('/pages/blog.php')->getContent());
    }

    public function test_alt_text_is_used_when_set_and_falls_back_to_title_when_blank(): void
    {
        GalleryItem::create([
            'image_path' => 'uploads/a.jpg', 'title' => 'Sports Day', 'sort_order' => 0,
            'alt_text' => 'Children playing football on a dusty field at sunset',
        ]);
        GalleryItem::create(['image_path' => 'uploads/b.jpg', 'title' => 'Fallback Title Here', 'sort_order' => 1]);

        $html = $this->get('/pages/gallery.php')->getContent();

        $this->assertStringContainsString('alt="Children playing football on a dusty field at sunset"', $html);
        $this->assertStringContainsString('alt="Fallback Title Here"', $html);
    }
}
