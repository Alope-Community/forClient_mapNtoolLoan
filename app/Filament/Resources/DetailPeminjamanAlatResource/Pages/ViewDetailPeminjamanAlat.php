<?php

namespace App\Filament\Resources\DetailPeminjamanAlatResource\Pages;

use App\Filament\Resources\DetailPeminjamanAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDetailPeminjamanAlat extends ViewRecord
{
    protected static string $resource = DetailPeminjamanAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
