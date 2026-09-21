<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This is NOT a payment gateway record — nothing here confirms money actually
        // moved. It's a donor voluntarily saying "I just sent this" so staff have
        // something to check against the real mobile money statement during
        // reconciliation. That distinction matters: the site never claims to have
        // verified or requested a payment, only to have received a note about one.
        Schema::create('donation_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('amount'); // free text — donor-entered, not validated against a real transaction
            $table->string('network'); // mpesa / tigopesa / airtel / halotel
            $table->string('reference')->nullable(); // SMS confirmation code, if the donor has one
            $table->boolean('reconciled')->default(false); // staff ticks this off once matched to the real statement
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_declarations');
    }
};
