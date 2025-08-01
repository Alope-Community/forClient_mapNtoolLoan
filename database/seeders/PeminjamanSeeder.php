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
        $today = \Carbon\Carbon::now()->startOfDay();

        $peminjaman = [
            // 1. Sudah lewat batas, belum dikembalikan => harus jadi overdue
            [
                'id_peminjam' => 2,
                'tanggal_pinjam' => $today->copy()->subDays(10),
                'tanggal_pengembalian' => $today->copy()->subDays(3),
                'status' => 'approved',
            ],
            // 2. Belum lewat batas, masih aktif
            [
                'id_peminjam' => 2,
                'tanggal_pinjam' => $today,
                'tanggal_pengembalian' => $today->copy()->addDays(3),
                'status' => 'approved',
            ],
            // 3. Sudah dikembalikan
            [
                'id_peminjam' => 2,
                'tanggal_pinjam' => $today->copy()->subDays(7),
                'tanggal_pengembalian' => $today->copy()->subDays(1),
                'status' => 'returned',
            ],
            [
                'id_peminjam' => 2,
                'tanggal_pinjam' => $today->copy()->subDays(7),
                'tanggal_pengembalian' => $today->copy()->addDays(1),
                'status' => 'approved',
            ],
            // 4. Masih pending dan belum dimulai
            [
                'id_peminjam' => 2,
                'tanggal_pinjam' => $today->copy()->addDays(2),
                'tanggal_pengembalian' => $today->copy()->addDays(5),
                'status' => 'pending',
            ],
            // 5. Sudah lewat, status tetap pending (opsional, jika ingin test logika lain)
            [
                'id_peminjam' => 2,
                'tanggal_pinjam' => $today->copy()->subDays(5),
                'tanggal_pengembalian' => $today->copy()->subDays(1),
                'status' => 'pending',
            ],
        ];

        foreach ($peminjaman as $item) {
            Peminjaman::create($item);
        }
    }
}
