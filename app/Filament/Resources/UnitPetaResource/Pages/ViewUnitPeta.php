<?php

namespace App\Filament\Resources\UnitPetaResource\Pages;

use App\Filament\Resources\UnitPetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitPeta extends ViewRecord
{
    protected static string $resource = UnitPetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
