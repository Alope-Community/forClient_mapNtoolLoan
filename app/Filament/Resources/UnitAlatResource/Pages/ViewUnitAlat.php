<?php

namespace App\Filament\Resources\UnitAlatResource\Pages;

use App\Filament\Resources\UnitAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;

class ViewUnitAlat extends ViewRecord
{
    protected static string $resource = UnitAlatResource::class;

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
                Section::make('Informasi Alat')
                    ->schema([
                        Group::make([
                            TextEntry::make('alat.nama')
                                ->label('Nama Alat')
                                ->columnSpanFull()
                                ->color('primary'),

                            TextEntry::make('alat.deskripsi')
                                ->label('Deskripsi Alat')
                                ->markdown()
                                ->columnSpanFull(),
                        ])->columns(1),
                    ]),

                Section::make('Informasi Serial Number')
                    ->schema([
                        Group::make([
                            TextEntry::make('serialNumber.serial_number')
                                ->label('Nomor Serial')
                                ->copyable()
                                ->color('gray'),

                            TextEntry::make('serialNumber.deskripsi')
                                ->label('Deskripsi Serial')
                                ->markdown(),
                        ])->columns(1),
                    ]),

                Section::make('Detail Unit Alat')
                    ->schema([
                        Group::make([
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
                                ->color(fn($state) => $state ? 'warning' : 'success'),
                        ])->columns(1),
                    ]),

                Section::make('Gambar Unit')->schema([
                    Group::make([
                        TextEntry::make('alat.gambar')
                            ->label('File Gambar Alat')
                            ->formatStateUsing(fn($state) => $state ? 'Lihat File' : '-')
                            ->url(fn($state) => $state ? asset('storage/' . $state) : null, true)
                            ->openUrlInNewTab()
                    ])
                ])
            ]);
    }
}
