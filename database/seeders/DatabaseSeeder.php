<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AlatSeeder::class,
            PetaSeeder::class,
            SerialNumberSeeder::class,
            UnitAlatSeeder::class,
            UnitPetaSeeder::class,
            PeminjamanSeeder::class,
            DetailPeminjamanAlatSeeder::class,
            DetailPeminjamanPetaSeeder::class,
        ]);
    }
}
