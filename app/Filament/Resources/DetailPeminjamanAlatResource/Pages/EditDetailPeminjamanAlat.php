<?php

namespace App\Filament\Resources\DetailPeminjamanAlatResource\Pages;

use App\Filament\Resources\DetailPeminjamanAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetailPeminjamanAlat extends EditRecord
{
    protected static string $resource = DetailPeminjamanAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
