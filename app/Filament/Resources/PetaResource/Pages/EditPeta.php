<?php

namespace App\Filament\Resources\PetaResource\Pages;

use App\Filament\Resources\PetaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeta extends EditRecord
{
    protected static string $resource = PetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
