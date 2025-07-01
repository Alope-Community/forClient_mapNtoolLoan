<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use App\Models\DetailPeminjamanAlat;
use App\Models\DetailPeminjamanPeta;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->hasRole('karyawan')) {
            $data['id_peminjam'] = auth()->id();
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $data = $this->form->getState();

        if (!empty($data['id_unit_alat'])) {
            foreach ($data['id_unit_alat'] as $unitAlatId) {
                DetailPeminjamanAlat::create([
                    'id_peminjaman' => $record->id,
                    'id_unit_alat' => $unitAlatId,
                ]);
                // \App\Models\UnitAlat::where('id', $unitAlatId)->update(['is_dipinjam' => true]);
            }
        }

        if (!empty($data['id_unit_peta'])) {
            foreach ($data['id_unit_peta'] as $unitPetaId) {
                DetailPeminjamanPeta::create([
                    'id_peminjaman' => $record->id,
                    'id_unit_peta' => $unitPetaId,
                ]);
                // \App\Models\UnitPeta::where('id', $unitPetaId)->update(['is_dipinjam' => true]);
            }
        }
    }
}
