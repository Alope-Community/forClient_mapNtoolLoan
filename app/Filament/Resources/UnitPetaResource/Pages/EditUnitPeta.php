<?php

namespace App\Filament\Resources\UnitPetaResource\Pages;

use App\Filament\Resources\UnitPetaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitPeta extends EditRecord
{
    protected static string $resource = UnitPetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
