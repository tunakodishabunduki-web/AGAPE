<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The homepage banner only ever needed a one-line `message`. The dedicated
        // /pages/updates.php page shows fuller cards (title + details + photo) —
        // this was missing from the original migration, found while wiring the
        // public content endpoint against what that page's JS actually expects.
        Schema::table('site_updates', function (Blueprint $table) {
            $table->string('title')->nullable()->after('message');
            $table->text('details')->nullable()->after('title');
            $table->string('image_path')->nullable()->after('details');
        });
    }

    public function down(): void
    {
        Schema::table('site_updates', function (Blueprint $table) {
            $table->dropColumn(['title', 'details', 'image_path']);
        });
    }
};
