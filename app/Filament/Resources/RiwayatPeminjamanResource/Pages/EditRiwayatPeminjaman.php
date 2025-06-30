<?php

namespace App\Filament\Resources\RiwayatPeminjamanResource\Pages;

use App\Filament\Resources\RiwayatPeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatPeminjaman extends EditRecord
{
    protected static string $resource = RiwayatPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
