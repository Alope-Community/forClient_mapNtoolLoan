<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;

class ViewPeminjaman extends ViewRecord
{
    protected static string $resource = PeminjamanResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Peminjaman')
                    ->schema([
                        Group::make([
                            TextEntry::make('tanggal_pinjam')
                                ->label('Tanggal Pinjam')
                                ->date()
                                ->color('primary'),
                            TextEntry::make('tanggal_pengembalian')
                                ->label('Tanggal Pengembalian')
                                ->date()
                                ->color('secondary'),
                        ]),
                        Group::make([
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color('warning'),
                        ]),
                    ]),

                Section::make('Unit Alat')
                    ->schema([
                        RepeatableEntry::make('detailPeminjamanAlat')
                            ->label('Daftar Unit Alat')
                            ->schema([
                                TextEntry::make('unitAlat.alat.nama')->label('Nama Alat'),
                                TextEntry::make('unitAlat.serialNumber.serial_number')->label('Serial Number'),
                                TextEntry::make('unitAlat.kondisi')->label('Kondisi'),
                                TextEntry::make('unitAlat.lokasi')->label('Lokasi'),
                            ])
                            ->columns(2),
                    ])->visible(fn($record) => $record->detailPeminjamanAlat->isNotEmpty()),

                Section::make('Unit Peta')
                    ->schema([
                        RepeatableEntry::make('detailPeminjamanPeta')
                            ->label('Daftar Unit Peta')
                            ->schema([
                                TextEntry::make('unitPeta.peta.nama')->label('Nama Peta'),
                                TextEntry::make('unitPeta.kondisi')->label('Kondisi'),
                                TextEntry::make('unitPeta.lokasi')->label('Lokasi'),
                            ])
                            ->columns(2),
                    ])->visible(fn($record) => $record->detailPeminjamanPeta->isNotEmpty()),

            ]);
    }
}
