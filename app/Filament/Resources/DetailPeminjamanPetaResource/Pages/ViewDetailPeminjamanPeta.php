<?php

namespace App\Filament\Resources\DetailPeminjamanPetaResource\Pages;

use App\Filament\Resources\DetailPeminjamanPetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDetailPeminjamanPeta extends ViewRecord
{
    protected static string $resource = DetailPeminjamanPetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
