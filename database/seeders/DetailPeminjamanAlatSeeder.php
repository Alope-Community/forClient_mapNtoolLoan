<?php

namespace Database\Seeders;

use App\Models\DetailPeminjamanAlat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailPeminjamanAlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $detailPeminjaman = [
            [
                'id_peminjaman' => 1,
                'id_unit_alat' => 1, // Kompas
            ]
        ];

        foreach ($detailPeminjaman as $item) {
            DetailPeminjamanAlat::create($item);
        }
    }
}
