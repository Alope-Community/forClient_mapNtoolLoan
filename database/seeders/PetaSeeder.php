<?php

namespace Database\Seeders;

use App\Models\Peta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peta = [
            [
                'nama' => 'Peta Topografi 1:25000',
                'deskripsi' => 'Topografi wilayah A',
                'nomor' => 'TPG-25000-A',
                'provinsi' => 'DIY',
                'kabupaten' => 'Sleman',
                'gambar' => 'peta1.jpg'
            ]
        ];

        foreach ($peta as $item) {
            Peta::create($item);
        }
    }
}
