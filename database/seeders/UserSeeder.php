<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@salah.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

         // Instructors
        User::factory()->count(3)->create(['role' => 'instructor']);

        // Students
        User::factory()->count(10)->create(['role' => 'student']);
    }
}
