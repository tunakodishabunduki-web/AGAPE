<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_updates', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            // Old system deleted expired updates lazily on every read().
            // We keep that exact behaviour (see App\Models\SiteUpdate::active()),
            // rather than adding a queue/cron job the admin would need to maintain.
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::create('whatsapp_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('label');         // e.g. "General enquiries"
            $table->string('phone_number');  // international format
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_contacts');
        Schema::dropIfExists('site_updates');
    }
};
