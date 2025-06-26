<?php

namespace App\Filament\Resources\PetaResource\Pages;

use App\Filament\Resources\PetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPetas extends ListRecords
{
    protected static string $resource = PetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
