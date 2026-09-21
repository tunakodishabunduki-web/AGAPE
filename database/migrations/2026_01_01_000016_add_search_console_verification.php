<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Just the content value Google Search Console gives you for the
            // HTML-tag verification method (Settings > Ownership verification).
            // Works for a URL-prefix property on a bare IP just as well as a
            // real domain — it's the "Domain property" method (DNS-based) that
            // needs an actual domain, which this site doesn't have yet.
            $table->string('search_console_verification')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('search_console_verification');
        });
    }
};
