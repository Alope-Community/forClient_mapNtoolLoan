<?php

namespace Database\Seeders;

use App\Models\UnitAlat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitAlat = [
            [
                'id_alat' => 1, // Kompas
                'id_serial_number' => 2,
                'kondisi' => 'baik',
                'lokasi' => 'Gudang A',
                'is_dipinjam' => false,
            ],
            [
                'id_alat' => 2, // GPS
                'id_serial_number' => 1,
                'kondisi' => 'baik',
                'lokasi' => 'Gudang B',
                'is_dipinjam' => false,
            ]
        ];

        foreach ($unitAlat as $item) {
            UnitAlat::create($item);
        }
    }
}
