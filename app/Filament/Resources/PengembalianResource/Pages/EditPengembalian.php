<?php

namespace App\Filament\Resources\PengembalianResource\Pages;

use App\Filament\Resources\PengembalianResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

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

        $recepients = User::withoutRole('karyawan')->get();

        $namaPeminjam = auth()->user()->nama;
        $tanggalPengembalian = \Carbon\Carbon::parse($record->updated_at)->translatedFormat('d F Y');
        $jumlahAlat = $record->detailPeminjamanAlat()->count();
        $jumlahPeta = $record->detailPeminjamanPeta()->count();

        $bodyMessage = new HtmlString("
        <strong>{$namaPeminjam}</strong> telah mengajukan pengembalian pada tanggal <strong>{$tanggalPengembalian}</strong>.<br>
        Jumlah Alat: <strong>{$jumlahAlat}</strong><br>
        Jumlah Peta: <strong>{$jumlahPeta}</strong><br>
        <a href='" . route('filament.admin.resources.pengembalian.view', $record) . "' class='underline text-primary-600'>Lihat Detail</a>");

        foreach ($recepients as $user) {
            Notification::make()
                ->title('Pengajuan Pengembalian Baru')
                ->body($bodyMessage)
                ->sendToDatabase($user);
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
