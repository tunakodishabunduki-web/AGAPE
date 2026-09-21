<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // site-data.js already calls POST /api/analytics on every single
        // pageview (once per page per browser session, via a sessionStorage
        // guard) and on a handful of specific actions (donation_click,
        // volunteer_application_submitted, etc.) — that call has existed on
        // the frontend with no backend behind it until now.
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'pageview', 'donation_click', 'contact_conversion', ...
            $table->string('page')->nullable();
            $table->string('label')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
