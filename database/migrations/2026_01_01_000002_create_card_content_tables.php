<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // gallery, programs, leaders, blog all used to be arrays-of-arrays inside
        // one content.json ("shared card total" cap of 25 across all four).
        // Each now gets its own real table with its own sort_order column —
        // the 25-across-all-four cap is enforced in the controller, same rule as before.

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('title');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('stat_number');   // e.g. "520+"
            $table->string('stat_label');    // e.g. "children supported"
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('leaders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->text('bio');
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();   // used in blog-post.php?id=
            $table->string('excerpt');
            $table->longText('body');
            $table->string('image_path')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('success_stories', function (Blueprint $table) {
            // this was "reviews" in the old content.json (renderStoriesHtml)
            $table->id();
            $table->string('name');
            $table->string('age_label'); // e.g. "Age 12, Class 6"
            $table->text('quote');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('success_stories');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('leaders');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('gallery_items');
    }
};
