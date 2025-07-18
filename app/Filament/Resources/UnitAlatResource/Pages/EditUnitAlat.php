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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['gambar_alat']) && !empty($data['id_alat'])) {
            $alat = \App\Models\Alat::find($data['id_alat']);
            if ($alat) {
                $alat->gambar = $data['gambar_alat'];
                $alat->save();
            }
        }

        unset($data['gambar_alat']);

        return $data;
    }
}
