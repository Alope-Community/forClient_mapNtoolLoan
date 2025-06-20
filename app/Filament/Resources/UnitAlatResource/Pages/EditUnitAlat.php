<?php

namespace App\Filament\Resources\UnitAlatResource\Pages;

use App\Filament\Resources\UnitAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitAlat extends EditRecord
{
    protected static string $resource = UnitAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
