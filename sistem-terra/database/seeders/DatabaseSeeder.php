<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat Akun Teknisi Otomatis
        \App\Models\User::create([
            'name' => 'Master Teknisi',
            'email' => 'admin@terra.com',
            'password' => bcrypt('12345678'), 
            'role' => 'teknisi',
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
