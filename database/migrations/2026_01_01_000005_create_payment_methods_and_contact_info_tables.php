<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            // One of: mpesa, tigopesa, airtel, halotel — enforced in PaymentMethodController,
            // not a DB enum, so adding a fifth network later is a one-line change, not a migration.
            $table->string('network');
            $table->string('lipa_number');
            $table->string('account_name')->nullable();
            // The network donors see FIRST/active-by-default in the pay modal. Only one row
            // can be true at a time — enforced in the controller (setting a new primary
            // clears the old one), same idea as a "default payment method" toggle.
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
        // Hard cap of 2 rows total — enforced in PaymentMethodController::store(), not here,
        // because MySQL has no native "max row count" constraint.

        Schema::create('contact_info', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('regional_offices')->nullable();
            $table->string('availability')->nullable();
            $table->text('intro')->nullable();
            $table->timestamps();
        });
        // Singleton, same pattern as hero_content — see App\Models\ContactInfo::current().
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_info');
        Schema::dropIfExists('payment_methods');
    }
};
