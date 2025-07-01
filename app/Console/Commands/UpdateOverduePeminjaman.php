<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $overdues = \App\Models\Peminjaman::where('status', '!=', 'returned')
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
            }

        $this->info("Updated $updatedCount peminjaman to overdue and released all related units.");
    }
}
