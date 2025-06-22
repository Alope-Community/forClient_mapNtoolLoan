<?php

namespace App\Filament\Resources\DetailPeminjamanAlatResource\Pages;

use App\Filament\Resources\DetailPeminjamanAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetailPeminjamanAlats extends ListRecords
{
    protected static string $resource = DetailPeminjamanAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
