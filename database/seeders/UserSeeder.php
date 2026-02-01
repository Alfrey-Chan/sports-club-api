<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'phone_number' => '0123456789',
        ]);

        User::create([
            'email' => 'demo@demo.com',
            'password' => 'demo',
            'phone_number' => '1234567890',
        ]);
    }
}
