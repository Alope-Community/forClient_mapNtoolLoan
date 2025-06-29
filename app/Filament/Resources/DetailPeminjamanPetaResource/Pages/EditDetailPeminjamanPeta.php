<?php

namespace App\Filament\Resources\DetailPeminjamanPetaResource\Pages;

use App\Filament\Resources\DetailPeminjamanPetaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetailPeminjamanPeta extends EditRecord
{
    protected static string $resource = DetailPeminjamanPetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
