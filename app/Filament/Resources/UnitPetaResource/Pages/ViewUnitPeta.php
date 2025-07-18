<?php

namespace App\Filament\Resources\UnitPetaResource\Pages;

use App\Filament\Resources\UnitPetaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Storage;

class ViewUnitPeta extends ViewRecord
{
    protected static string $resource = UnitPetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Peta')
                    ->schema([
                        TextEntry::make('peta.nama')
                            ->label('Nama Peta')
                            ->color('primary'),

                        TextEntry::make('peta.deskripsi')
                            ->label('Deskripsi')
                            ->markdown(),

                        TextEntry::make('peta.nomor')
                            ->label('Nomor'),

                        TextEntry::make('peta.provinsi')
                            ->label('Provinsi'),

                        TextEntry::make('peta.kabupaten')
                            ->label('Kabupaten'),

                        TextEntry::make('peta.gambar')
                            ->label('File Peta (PDF)')
                            ->formatStateUsing(fn($state) => $state ? 'Lihat File' : '-')
                            ->url(fn($record) => Storage::url($record->peta->gambar))
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                Section::make('Detail Unit Peta')
                    ->schema([
                        TextEntry::make('kondisi')
                            ->label('Kondisi')
                            ->badge()
                            ->color(fn(string $state) => match ($state) {
                                'baik' => 'success',
                                'rusak' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('lokasi')
                            ->label('Lokasi Penyimpanan'),

                        TextEntry::make('is_dipinjam')
                            ->label('Status Peminjaman')
                            ->formatStateUsing(fn($state) => $state ? 'Sedang Dipinjam' : 'Tersedia')
                            ->badge()
                            ->color(fn($state) => $state ? 'danger' : 'success'),
                    ])
                    ->columns(1),
            ]);
    }
}
