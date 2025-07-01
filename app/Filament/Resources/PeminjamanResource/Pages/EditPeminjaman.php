<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeminjaman extends EditRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->form->getState();

        if (!empty($data['status'])) {
            $status = $data['status'];

            $alatIds = $record->detailPeminjamanAlat->pluck('id_unit_alat')->filter();
            
            if ($status === 'returned' || $status === 'rejected') {
                foreach ($alatIds as $unitAlatId) {
                    \App\Models\UnitAlat::where('id', $unitAlatId)->update(['is_dipinjam' => false]);
                }
            } elseif ($status === 'approved') {
                foreach ($alatIds as $unitAlatId) {
                    \App\Models\UnitAlat::where('id', $unitAlatId)->update(['is_dipinjam' => true]);
                }
            }
            
            $petaIds = $record->detailPeminjamanPeta->pluck('id_unit_peta')->filter();

            if ($status === 'returned' || $status === 'rejected') {
                foreach ($petaIds as $unitPetaId) {
                    \App\Models\UnitPeta::where('id', $unitPetaId)->update(['is_dipinjam' => false]);
                }
            } elseif ($status === 'approved') {
                foreach ($petaIds as $unitPetaId) {
                    \App\Models\UnitPeta::where('id', $unitPetaId)->update(['is_dipinjam' => true]);
                }
            }

            $record->status = $status;
            $record->save();
        }
    }
}
