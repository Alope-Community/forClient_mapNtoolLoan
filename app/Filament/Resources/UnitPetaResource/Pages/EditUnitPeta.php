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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['gambar_peta']) && !empty($data['id_peta'])) {
            $peta = \App\Models\Peta::find($data['id_peta']);
            if ($peta) {
                $peta->gambar = $data['gambar_peta'];
                $peta->save();
            }
        }

        unset($data['gambar_peta']);

        return $data;
    }
}
