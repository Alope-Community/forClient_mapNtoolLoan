<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Karyawan', User::role('karyawan')->count())
                ->icon('heroicon-o-users'),
            Stat::make('Total Peminjaman Alat', Peminjaman::count())
                ->icon('heroicon-o-bookmark'),
            Stat::make('Total Pengembalian Alat', Peminjaman::where('status', 'returned')->count())
                ->icon('heroicon-o-arrow-path'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
