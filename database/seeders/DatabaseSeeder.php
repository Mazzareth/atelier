<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        User::firstOrCreate([
            'email' => 'admin@atelier.app',
        ], [
            'name' => 'Platform Admin',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'active_profile' => 'commissioner', // default to play mode
        ]);

        // 2. Artist
        User::firstOrCreate([
            'email' => 'artist@atelier.app',
        ], [
            'name' => 'Gote The Creator',
            'password' => Hash::make('password'),
            'role' => UserRole::Artist,
            'active_profile' => 'artist', // default to work mode
        ]);

        // 3. Commissioner
        User::firstOrCreate([
            'email' => 'buyer@atelier.app',
        ], [
            'name' => 'Happy Commissioner',
            'password' => Hash::make('password'),
            'role' => UserRole::Commissioner,
            'active_profile' => 'commissioner',
        ]);
    }
}
