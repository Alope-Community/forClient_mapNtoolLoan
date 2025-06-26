<?php

namespace App\Filament\Resources\UnitPetaResource\Pages;

use App\Filament\Resources\UnitPetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitPetas extends ListRecords
{
    protected static string $resource = UnitPetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
