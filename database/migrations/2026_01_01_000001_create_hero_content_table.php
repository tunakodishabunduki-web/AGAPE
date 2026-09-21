<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only ever one row in this table — the homepage hero is a singleton,
        // not a list, so it doesn't need the sort_order pattern the card tables use.
        Schema::create('hero_content', function (Blueprint $table) {
            $table->id();
            $table->string('headline');
            $table->string('subheadline')->nullable();
            $table->string('background_image')->nullable(); // path under assets/uploads, same as before
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_content');
    }
};
