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
            ->update(['status' => 'overdue']);

        $this->info("Updated $overdues records to overdue.");
    }
}
