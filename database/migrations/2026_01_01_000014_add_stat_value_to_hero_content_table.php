<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Found while wiring up real server-side rendering for the homepage
        // hero: index.php actually splits the hero into a large stat number
        // ("520+") and a separate headline span ("Children Given a Second
        // Chance") — not one combined headline as the original hero_content
        // migration assumed. Without this field there was no way for the
        // admin to change the prominent "520+" number at all.
        Schema::table('hero_content', function (Blueprint $table) {
            $table->string('stat_value')->default('520+')->after('headline');
        });
    }

    public function down(): void
    {
        Schema::table('hero_content', function (Blueprint $table) {
            $table->dropColumn('stat_value');
        });
    }
};
