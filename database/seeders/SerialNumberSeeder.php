<?php

namespace Database\Seeders;

use App\Models\SerialNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SerialNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serialNumber = [
            [
                'serial_number' => 'SN001-GPS',
                'deskripsi' => 'Serial GPS A',
            ],
            [
                'serial_number' => 'SN002-KPS',
                'deskripsi' => 'Serial Kompas A',
            ]
        ];

        foreach ($serialNumber as $item) {
            SerialNumber::create($item);
        }
    }
}
