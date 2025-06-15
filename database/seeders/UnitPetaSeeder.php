<?php

namespace Database\Seeders;

use App\Models\UnitPeta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitPetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitPeta = [
            [
                'id_peta' => 1,
                'kondisi' => 'baik',
                'lokasi' => 'Rak A',
                'is_dipinjam' => false,
            ]
        ];

        foreach ($unitPeta as $item) {
            UnitPeta::create($item);
        }
    }
}
