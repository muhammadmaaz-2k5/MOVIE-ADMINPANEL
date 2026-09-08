<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the initial encrypted admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Engora Admin',
                'email' => 'admin@engora.com',
                'password' => Hash::make('Engora@2026!'),
                'is_admin' => true,
            ]
        );
    }
}
