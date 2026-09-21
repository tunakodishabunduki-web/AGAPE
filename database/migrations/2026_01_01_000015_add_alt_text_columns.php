<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullable everywhere — when blank, rendering falls back to the item's
        // title/name (already reasonable alt text), but a real description here
        // is what actually helps screen readers and image search, e.g. "Two
        // students studying together under a mango tree" instead of "Gallery Photo 3".
        foreach (['gallery_items', 'programs', 'leaders', 'blog_posts', 'success_stories'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('alt_text')->nullable();
            });
        }

        Schema::table('hero_content', function (Blueprint $t) {
            $t->string('background_image_alt')->nullable();
        });

        Schema::table('site_settings', function (Blueprint $t) {
            $t->string('seo_image_alt')->nullable();
        });
    }

    public function down(): void
    {
        foreach (['gallery_items', 'programs', 'leaders', 'blog_posts', 'success_stories'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn('alt_text'));
        }
        Schema::table('hero_content', fn (Blueprint $t) => $t->dropColumn('background_image_alt'));
        Schema::table('site_settings', fn (Blueprint $t) => $t->dropColumn('seo_image_alt'));
    }
};
