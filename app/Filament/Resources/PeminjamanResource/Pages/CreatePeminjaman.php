<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use App\Models\DetailPeminjamanAlat;
use App\Models\DetailPeminjamanPeta;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

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
        // $data = $this->form->getState();

        // if (!empty($data['id_unit_alat'])) {
        //     foreach ($data['id_unit_alat'] as $unitAlatId) {
        //         DetailPeminjamanAlat::create([
        //             'id_peminjaman' => $record->id,
        //             'id_unit_alat' => $unitAlatId,
        //         ]);
        //         // \App\Models\UnitAlat::where('id', $unitAlatId)->update(['is_dipinjam' => true]);
        //     }
        // }

        // if (!empty($data['id_unit_peta'])) {
        //     foreach ($data['id_unit_peta'] as $unitPetaId) {
        //         DetailPeminjamanPeta::create([
        //             'id_peminjaman' => $record->id,
        //             'id_unit_peta' => $unitPetaId,
        //         ]);
        //         // \App\Models\UnitPeta::where('id', $unitPetaId)->update(['is_dipinjam' => true]);
        //     }
        // }

        $recepients = User::withoutRole('karyawan')->get();

        $namaPeminjam = auth()->user()->nama;
        $tanggalPeminjaman = \Carbon\Carbon::parse($record->tanggal_pinjam)->translatedFormat('d F Y');
        $tanggalPengembalian = \Carbon\Carbon::parse($record->tanggal_pengembalian)->translatedFormat('d F Y');
        $jumlahAlat = $record->detailPeminjamanAlat()->count();
        $jumlahPeta = $record->detailPeminjamanPeta()->count();

        $bodyMessage = new HtmlString("
        <strong>{$namaPeminjam}</strong> telah mengajukan peminjaman pada tanggal <strong>{$tanggalPeminjaman}</strong> sampai dengan <strong>{$tanggalPengembalian}</strong>.<br>
        Jumlah Alat: <strong>{$jumlahAlat}</strong><br>
        Jumlah Peta: <strong>{$jumlahPeta}</strong><br>
        <a href='" . route('filament.admin.resources.peminjaman.view', $record) . "' class='underline text-primary-600'>Lihat Detail</a>");

        foreach ($recepients as $user) {
            Notification::make()
                ->title('Pengajuan Peminjaman Baru')
                ->body($bodyMessage)
                ->sendToDatabase($user);
        }
    }
}
