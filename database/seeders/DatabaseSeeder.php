<?php

namespace Database\Seeders;

use App\Models\StaffUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // Creates the very first admin account from .env values, exactly once.
    // Run with: php artisan db:seed
    public function run(): void
    {
        StaffUser::firstOrCreate(
            ['username' => strtolower(env('BOOTSTRAP_ADMIN_USERNAME', 'admin'))],
            [
                'password' => Hash::make(env('BOOTSTRAP_ADMIN_PASSWORD')),
                'role' => 'admin',
                'must_change_password' => true, // forced to set a real password on first login
            ]
        );
    }
}
