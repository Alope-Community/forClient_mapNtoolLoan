<?php

namespace App\Console\Commands;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\HtmlString;

class UpdateOverduePeminjaman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-overdue-peminjaman';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Overdue Peminjaman';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overdues = \App\Models\Peminjaman::with([
            'user',
            'detailPeminjamanAlat.unitAlat',
            'detailPeminjamanPeta.unitPeta',
        ])
            ->where('status', '!=', 'returned')
            ->whereDate('tanggal_pengembalian', '<', now())
            ->get();

        $updatedCount = 0;

        foreach ($overdues as $peminjamanOverdue) {
            $peminjamanOverdue->update(['status' => 'overdue']);

            foreach ($peminjamanOverdue->detailPeminjamanAlat as $detail) {
                $detail->unitAlat?->update(['is_dipinjam' => false]);
            }

            foreach ($peminjamanOverdue->detailPeminjamanPeta as $detail) {
                $detail->unitPeta?->update(['is_dipinjam' => false]);
            }

            $updatedCount++;

            $recepients = User::withoutRole('karyawan')->get();

            $namaPeminjam = $peminjamanOverdue->user->nama;
            $batasPeminjaman = \Carbon\Carbon::parse($peminjamanOverdue->tanggal_pengembalian)->translatedFormat('d F Y');
            $jumlahAlat = $peminjamanOverdue->detailPeminjamanAlat->count();
            $jumlahPeta = $peminjamanOverdue->detailPeminjamanPeta->count();

            $bodyMessage = new HtmlString("
            <strong>{$namaPeminjam}</strong> telah terlambat mengembalikan barang sejak tanggal <strong>{$batasPeminjaman}</strong>.<br>
            Jumlah Alat: <strong>{$jumlahAlat}</strong><br>
            Jumlah Peta: <strong>{$jumlahPeta}</strong><br>
            <a href='" . route('filament.admin.resources.peminjaman.view', $peminjamanOverdue) . "' class='underline text-primary-600'>Lihat Detail</a>");

            foreach ($recepients as $user) {
                Notification::make()
                    ->title('Peminjaman Melewati Batas Waktu')
                    ->body($bodyMessage)
                    ->sendToDatabase($user);
            }
        }

        $this->info("Updated $updatedCount peminjaman to overdue and released all related units.");
    }
}
