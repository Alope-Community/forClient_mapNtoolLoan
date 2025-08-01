<?php

namespace Database\Seeders;

use App\Models\DetailPeminjamanPeta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailPeminjamanPetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $detailPeminjaman = [
        //     [
        //         'id_peminjaman' => 1,
        //         'id_unit_peta' => 1,
        //     ]
        // ];

        // foreach ($detailPeminjaman as $item) {
        //     DetailPeminjamanPeta::create($item);
        // }

        for ($i = 1; $i <= 5; $i++) {
            DetailPeminjamanPeta::create([
                'id_peminjaman' => $i,
                'id_unit_peta' => 1,
            ]);
        }

    }
}
