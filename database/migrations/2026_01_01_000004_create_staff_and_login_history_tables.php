<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password'); // hashed with bcrypt via Laravel's Hash facade
            $table->string('role')->default('editor'); // 'admin' or 'editor' — role-based staff accounts
            $table->string('totp_secret')->nullable();       // base32 secret, set once confirmed
            $table->string('totp_pending_secret')->nullable(); // set during setup, before confirmation
            $table->boolean('totp_enabled')->default(false);
            $table->boolean('must_change_password')->default(true); // forces change on first login
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
        // Max 5 rows enforced in StaffController, same MAX_USERS=5 rule as the old server.js.

        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('attempted_username')->nullable(); // kept even on failed/unknown-user attempts
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->boolean('success');
            $table->string('failure_reason')->nullable(); // 'bad_password', 'bad_totp', etc.
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_history');
        Schema::dropIfExists('staff_users');
    }
};
