<?php

namespace App\Filament\Resources\RiwayatPeminjamanResource\Pages;

use App\Filament\Resources\RiwayatPeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPeminjamen extends ListRecords
{
    protected static string $resource = RiwayatPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
