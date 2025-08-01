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
        $today = \Carbon\Carbon::now()->startOfDay();

        $overdues = \App\Models\Peminjaman::with([
            'user',
            'detailPeminjamanAlat.unitAlat',
            'detailPeminjamanPeta.unitPeta',
        ])
            ->where('status', '!=', 'returned')
            ->whereDate('tanggal_pengembalian', '<', $today)
            ->get();

        $updatedCount = 0;

        // Ambil data yang jatuh tempo besok
        $reminderDate = \Carbon\Carbon::now()->addDay()->startOfDay();

        $upcomingReturns = \App\Models\Peminjaman::with([
            'user',
            'detailPeminjamanAlat.unitAlat',
            'detailPeminjamanPeta.unitPeta',
        ])
            ->where('status', '!=', 'returned')
            ->whereDate('tanggal_pengembalian', '=', $reminderDate)
            ->get();

        foreach ($upcomingReturns as $peminjaman) {
            $namaPeminjam = $peminjaman->user->nama;
            $batasPeminjaman = \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->translatedFormat('d F Y');
            $jumlahAlat = $peminjaman->detailPeminjamanAlat->count();
            $jumlahPeta = $peminjaman->detailPeminjamanPeta->count();

            $bodyMessageReminder = new HtmlString("
            Anda memiliki peminjaman yang akan jatuh tempo pada <strong>{$batasPeminjaman}</strong>.<br>
            Jumlah Alat: <strong>{$jumlahAlat}</strong><br>
            Jumlah Peta: <strong>{$jumlahPeta}</strong><br>
            <a href='" . route('filament.employee.resources.pengembalian.view', $peminjaman) . "' class='underline text-primary-600'>Lihat Detail</a>");

            Notification::make()
                ->title('Peminjaman Akan Jatuh Tempo')
                ->body($bodyMessageReminder)
                ->sendToDatabase($peminjaman->user);
        }

        foreach ($overdues as $peminjamanOverdue) {

            $peminjamanOverdue->update(['status' => 'overdue']);

            foreach ($peminjamanOverdue->detailPeminjamanAlat as $detail) {
                $detail->unitAlat?->update(['is_dipinjam' => false]);
            }

            foreach ($peminjamanOverdue->detailPeminjamanPeta as $detail) {
                $detail->unitPeta?->update(['is_dipinjam' => false]);
            }

            $updatedCount++;

            $officers = User::role(['admin', 'kepala'])->get();
            $employees = User::role('karyawan')->get();

            $namaPeminjam = $peminjamanOverdue->user->nama;
            $batasPeminjaman = \Carbon\Carbon::parse($peminjamanOverdue->tanggal_pengembalian)->translatedFormat('d F Y');
            $jumlahAlat = $peminjamanOverdue->detailPeminjamanAlat->count();
            $jumlahPeta = $peminjamanOverdue->detailPeminjamanPeta->count();

            $bodyMessageForOfficer = new HtmlString("
            <strong>{$namaPeminjam}</strong> telah terlambat mengembalikan barang sejak tanggal <strong>{$batasPeminjaman}</strong>.<br>
            Jumlah Alat: <strong>{$jumlahAlat}</strong><br>
            Jumlah Peta: <strong>{$jumlahPeta}</strong><br>
            <a href='" . route('filament.admin.resources.peminjaman.view', $peminjamanOverdue) . "' class='underline text-primary-600'>Lihat Detail</a>");

            $bodyMessageForEmployee = new HtmlString("
            Anda telah terlambat mengembalikan barang sejak tanggal <strong>{$batasPeminjaman}</strong>.<br>
            Jumlah Alat: <strong>{$jumlahAlat}</strong><br>
            Jumlah Peta: <strong>{$jumlahPeta}</strong><br>
            <a href='" . route('filament.employee.resources.riwayat-peminjaman.view', $peminjamanOverdue) . "' class='underline text-primary-600'>Lihat Detail</a>");

            foreach ($officers as $user) {
                Notification::make()
                    ->title('Peminjaman Melewati Batas Waktu')
                    ->body($bodyMessageForOfficer)
                    ->sendToDatabase($user);
            }

            foreach ($employees as $user) {
                if ($peminjamanOverdue->user->id === $user->id) {
                    Notification::make()
                        ->title('Peminjaman Melewati Batas Waktu')
                        ->body($bodyMessageForEmployee)
                        ->sendToDatabase($user);
                }
            }
        }

        $this->info("Updated $updatedCount peminjaman to overdue and released all related units.");
    }
}
