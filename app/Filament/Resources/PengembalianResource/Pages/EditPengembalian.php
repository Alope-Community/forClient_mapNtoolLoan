<?php

namespace App\Filament\Resources\PengembalianResource\Pages;

use App\Filament\Resources\PengembalianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengembalian extends EditRecord
{
    protected static string $resource = PengembalianResource::class;

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

            if ($status === 'returned') {
                foreach ($alatIds as $unitAlatId) {
                    \App\Models\UnitAlat::where('id', $unitAlatId)->update(['is_dipinjam' => false]);
                }
            }

            $petaIds = $record->detailPeminjamanPeta->pluck('id_unit_peta')->filter();

            if ($status === 'returned') {
                foreach ($petaIds as $unitPetaId) {
                    \App\Models\UnitPeta::where('id', $unitPetaId)->update(['is_dipinjam' => false]);
                }
            }

            $record->status = $status;
            $record->save();
        }
    }

    // protected function afterSave(): void
    // {
    //     $record = $this->record;
    //     $data = $this->form->getState();

    //     if (!empty($data['bukti_pengembalian'])) {
    //         $record->status = 'returned';
    //         $record->save();
    //     }
    // }
}
