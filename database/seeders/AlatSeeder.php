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
            ],
            [
                'nama' => 'GPS',
                'deskripsi' => 'GPS tangan',
            ],
        ];
        
        foreach ($alat as $item) {
            Alat::create($item);
        }
    }
}
