<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@example.test')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'Admin123@mail.com',
                'password' => Hash::make('password123'), // Selalu gunakan Hash::make()
            ]);
        }
    }
}
