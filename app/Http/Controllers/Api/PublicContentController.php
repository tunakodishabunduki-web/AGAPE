<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactInfo;
use App\Models\GalleryItem;
use App\Models\HeroContent;
use App\Models\Leader;
use App\Models\PaymentMethod;
use App\Models\Program;
use App\Models\SiteSetting;
use App\Models\SiteUpdate;
use App\Models\SuccessStory;
use App\Models\WhatsappContact;
use App\Services\PaymentMethodPublicShaper;

class PublicContentController extends Controller
{
    // GET /api/content — unauthenticated, read-only. This is the exact path
    // site-data.js already calls on every page load (AFF.load()), so nothing
    // in the frontend needed to change — only this endpoint needed to exist.
    public function index()
    {
        $hero = HeroContent::current();
        $contact = ContactInfo::current();
        $settings = SiteSetting::current();
        $paymentMethods = PaymentMethod::ordered()->get();

        return response()->json([
            'hero' => [
                'headline' => $hero->headline,
                'description' => $hero->subheadline,
                'image' => $hero->background_image,
                'imageAlt' => $hero->background_image_alt,
            ],
            // Top-level too: site-data.js's shared apply() function reads
            // data.heroImage (not data.hero.image) for the client-side hero
            // background fallback used on every page except the server-rendered
            // homepage. Both point at the same underlying value.
            'heroImage' => $hero->background_image,
            // Tuple shapes below match exactly what the existing public JS indexes into
            // (program[0], leader[3], etc.) — see PublicPageController's render*Html()
            // methods for the server-rendered equivalent used on the homepage itself.
            'programs' => Program::ordered()->get()->map(fn ($p) => [
                $p->title, $p->description, $p->stat_number, $p->stat_label, $p->image_path, $p->alt_text,
            ]),
            'leaders' => Leader::ordered()->get()->map(fn ($l) => [
                $l->name, $l->title, $l->bio, $l->photo_path, $l->alt_text,
            ]),
            'gallery' => GalleryItem::ordered()->get()->map(fn ($g) => [
                $g->image_path, $g->title, $g->caption, $g->alt_text,
            ]),
            'reviews' => SuccessStory::ordered()->get()->map(fn ($r) => [
                $r->name, $r->age_label, $r->quote, $r->image_path, $r->alt_text,
            ]),
            'blog' => BlogPost::published()->get()->map(fn ($post) => [
                'id' => $post->slug,
                'image' => $post->image_path,
                'date' => $post->published_at?->format('F Y'),
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'imageAlt' => $post->alt_text,
            ]),
            'updates' => SiteUpdate::active()->map(fn ($u) => [
                'id' => (string) $u->id,
                'title' => $u->title ?: $u->message,
                'details' => $u->details ?: $u->message,
                'message' => $u->message,
                'image' => $u->image_path,
                'imageAlt' => $u->alt_text,
                'expiresAt' => $u->expires_at->toIso8601String(),
            ]),
            'whatsappContacts' => WhatsappContact::all()->map(fn ($c) => [
                'label' => $c->label, 'number' => $c->phone_number,
            ]),
            'contact' => [
                'email' => $contact->email,
                'whatsapp' => $contact->whatsapp,
                'phone' => $contact->phone,
                'office' => $contact->office,
                'regionalOffices' => $contact->regional_offices,
                'availability' => $contact->availability,
                'intro' => $contact->intro,
            ],
            'donation' => [
                'lipaNamba' => PaymentMethodPublicShaper::shape($paymentMethods),
                // The frontend opens the pay modal on this network by default —
                // this is literally "clients should only see Mpesa on top" implemented.
                'primaryNetwork' => optional($paymentMethods->first())->network,
            ],
            // Matches the exact shape site-data.js's apply() already reads
            // (data.seo.homeTitle/homeDescription/shareImage) — that contract
            // existed on the frontend with nothing behind it until this endpoint.
            'seo' => [
                'homeTitle' => $settings->seo_title,
                'homeDescription' => $settings->seo_description,
                'shareImage' => $settings->seo_image,
                'shareImageAlt' => $settings->seo_image_alt,
            ],
            'settings' => [
                'orgName' => $settings->org_name,
                'tagline' => $settings->tagline,
                'footerAbout' => $settings->footer_about,
                'registrationNumber' => $settings->registration_number,
                'social' => array_filter([
                    'facebook' => $settings->social_facebook,
                    'twitter' => $settings->social_twitter,
                    'instagram' => $settings->social_instagram,
                    'linkedin' => $settings->social_linkedin,
                    'youtube' => $settings->social_youtube,
                ]),
            ],
        ]);
    }
}
