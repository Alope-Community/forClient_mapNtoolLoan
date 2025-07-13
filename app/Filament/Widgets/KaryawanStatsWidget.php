<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use App\Models\UnitAlat;
use App\Models\UnitPeta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KaryawanStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Alat yang Dipinjam', Peminjaman::where('id_peminjam', auth()->id())->count())
                ->icon('heroicon-o-bookmark'),
            Stat::make('Total Pengembalian Alat', Peminjaman::where('status', 'returned', auth()->id())->count())
                ->icon('heroicon-o-arrow-path'),
            Stat::make('Jumlah Alat yang Tersedia', UnitAlat::where('is_dipinjam', 0)->count())
                ->icon('heroicon-o-arrow-path'),
            Stat::make('Jumlah Peta yang Tersedia', UnitPeta::where('is_dipinjam', 0)->count())
                ->icon('heroicon-o-arrow-path'),
        ];
    }
}
