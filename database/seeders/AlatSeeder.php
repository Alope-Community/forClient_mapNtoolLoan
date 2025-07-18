<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alat = [
            [
                'nama' => 'Kompas',
                'deskripsi' => 'Kompas lapangan',
                'gambar' => 'kompas_lapangan.png',
            ],
            [
                'nama' => 'GPS',
                'deskripsi' => 'GPS tangan',
                'gambar' => 'gps_tangan.png'
            ],
        ];
        
        foreach ($alat as $item) {
            Alat::create($item);
        }
    }
}
