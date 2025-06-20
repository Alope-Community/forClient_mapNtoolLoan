<?php

namespace App\Filament\Resources\UnitAlatResource\Pages;

use App\Filament\Resources\UnitAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitAlats extends ListRecords
{
    protected static string $resource = UnitAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
