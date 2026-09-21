<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Singleton, same pattern as hero_content/contact_info. site-data.js's
        // apply() function already reads data.seo.homeTitle/homeDescription/
        // shareImage on every page load — that contract already existed on the
        // frontend, nothing in the backend ever supplied it until this table.
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('org_name')->default('Agape Family Foundation');
            $table->string('tagline')->nullable();
            $table->text('footer_about')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->string('seo_image')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_linkedin')->nullable();
            $table->string('social_youtube')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
