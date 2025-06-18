<?php

namespace App\Filament\Resources\SerialNumberResource\Pages;

use App\Filament\Resources\SerialNumberResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSerialNumber extends ViewRecord
{
    protected static string $resource = SerialNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
