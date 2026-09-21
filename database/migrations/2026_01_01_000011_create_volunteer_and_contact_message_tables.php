<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The volunteer page's form previously had no submit handler at all —
        // clicking "Submit Application" just reloaded the page with nothing saved.
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('area'); // education / healthcare / community / admin / consulting / events
            $table->string('availability'); // flexible / weekends / weekdays / evenings
            $table->text('message');
            $table->boolean('read')->default(false); // powers the unread-count badge on the bell icon
            $table->timestamps();
        });

        // The contact page's AFF.sendMessage() call was already posting to
        // /api/messages — an endpoint that didn't exist anywhere in the backend.
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('volunteer_applications');
    }
};
