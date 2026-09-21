<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    // GET /sitemap.xml — dynamically generated, not a static file, so a new
    // blog post appears here automatically the next time a crawler fetches it.
    // Uses the url() helper so this is correct under any deployment,
    // subdirectory included, with zero hardcoded domain or path.
    public function index(): Response
    {
        $staticPages = [
            ['url' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => url('/pages/about.php'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => url('/pages/gallery.php'), 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['url' => url('/pages/leadership.php'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => url('/pages/blog.php'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => url('/pages/updates.php'), 'priority' => '0.7', 'changefreq' => 'daily'],
            ['url' => url('/pages/volunteer.php'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => url('/pages/contact.php'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => url('/pages/privacy.php'), 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        $posts = BlogPost::published()->get()->map(fn ($post) => [
            'url' => url('/pages/blog-post.php').'?id='.urlencode($post->slug),
            'priority' => '0.6',
            'changefreq' => 'monthly',
            'lastmod' => $post->updated_at->toAtomString(),
        ]);

        $entries = collect($staticPages)->map(fn ($p) => $p + ['lastmod' => now()->toAtomString()])->concat($posts);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($entries as $entry) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($entry['url'], ENT_XML1).'</loc>'."\n";
            $xml .= '    <lastmod>'.$entry['lastmod'].'</lastmod>'."\n";
            $xml .= '    <changefreq>'.$entry['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$entry['priority'].'</priority>'."\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
