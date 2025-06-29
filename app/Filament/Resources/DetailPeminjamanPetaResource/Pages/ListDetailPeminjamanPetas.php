<?php

namespace App\Filament\Resources\DetailPeminjamanPetaResource\Pages;

use App\Filament\Resources\DetailPeminjamanPetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailPeminjamanPetas extends ListRecords
{
    protected static string $resource = DetailPeminjamanPetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
