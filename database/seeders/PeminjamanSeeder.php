<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peminjaman = [
            [
                'id_peminjam' => 2,
                'tanggal_pinjam' => now(),
                'tanggal_pengembalian' => now()->addDays(3),
                'status' => 'borrowed',
            ]
        ];

        foreach ($peminjaman as $item) {
            Peminjaman::create($item);
        }
    }
}
