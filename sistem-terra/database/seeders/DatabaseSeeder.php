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
            'email' => 'inassamara07@gmail.com',
            'password' => bcrypt('12345678'), 
            'role' => 'teknisi',
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'inassamarataqia@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'petani',
        ]);
        $this->createKodeUnikPetani();
    }

    /**
     * Buat data kode unik petani dummy
     */
    private function createKodeUnikPetani(): void
    {
        $kodeUnikPetanis = [
            'TERRA167' => 'Inas',
            'TERRA125' => 'Rafi', 
            'TERRA015' => 'Arya',
        ];
        \Cache::forever('kode_unik_petanis', $kodeUnikPetanis);
        $this->command->info('Kode Unik Petani berhasil dibuat!');
        $this->command->info('Kode yang tersedia:');
        foreach ($kodeUnikPetanis as $kode => $nama) {
            $this->command->info("- $kode ($nama)");
        }
    }
}
