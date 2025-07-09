<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailPeminjamanAlatResource\Pages;
use App\Models\DetailPeminjamanAlat;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailPeminjamanAlatResource extends Resource
{
    protected static ?string $model = DetailPeminjamanAlat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema(function () use ($form) {
            $operation = $form->getOperation();

            $isView = $operation === 'view';
            $isEdit = $operation === 'edit';
            $isCreate = $operation === 'create';

            return array_filter([
                Select::make('id_peminjaman')
                    ->label('Nama Peminjam')
                    ->options(fn() => \App\Models\Peminjaman::with('user')->get()->pluck('user.nama', 'id'))
                    ->searchable()
                    ->disabled($isView)
                    ->required(),
                Select::make('id_unit_alat')
                    ->label('Nomor Serial & Nama Alat')
                    ->options(
                        \App\Models\UnitAlat::with(['serialNumber', 'alat'])->get()
                            ->mapWithKeys(fn($unit) => [
                                $unit->id => ($unit->serialNumber->serial_number ?? 'Serial -')
                                    . ' | '
                                    . ($unit->alat->nama ?? 'Alat -'),
                            ])
                    )
                    ->searchable()
                    ->disabled($isView)
                    ->required(),
                $isView
                    ? Placeholder::make('nama_alat')
                    ->label('Nama Alat')
                    ->content(fn($record) => optional($record?->unitAlat?->alat)->nama ?? '-')
                    : null,
                $isView
                    ? Placeholder::make('serial_number')
                    ->label('Nomor Serial')
                    ->content(fn($record) => optional($record?->unitAlat?->serialNumber)->serial_number ?? '-')
                    : null,
                Placeholder::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->content(
                        fn($record) =>
                        optional($record?->peminjaman)->tanggal_pinjam
                            ? \Carbon\Carbon::parse($record->peminjaman->tanggal_pinjam)->format('d-m-Y')
                            : '-'
                    ),
                Placeholder::make('tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->content(
                        fn($record) =>
                        optional($record?->peminjaman)->tanggal_pengembalian
                            ? \Carbon\Carbon::parse($record->peminjaman->tanggal_pengembalian)->format('d-m-Y')
                            : '-'
                    ),
                Placeholder::make('kondisi_alat')
                    ->label('Kondisi Alat')
                    ->content(fn($record) => match (optional($record?->unitAlat)->kondisi) {
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        default => 'Tidak Diketahui',
                    }),
                Placeholder::make('status_peminjaman')
                    ->label('Status Peminjaman')
                    ->content(fn($record) => match (optional($record?->peminjaman)->status) {
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        default => 'Tidak Diketahui',
                    }),
            ]);
        });
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        return $table
            ->columns([
                TextColumn::make('peminjaman.user.nama')
                    ->label('Nama Peminjam'),
                TextColumn::make('unitAlat.alat.nama')
                    ->label('Alat yang Dipinjam'),
                TextColumn::make('peminjaman.tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date(),
                TextColumn::make('peminjaman.tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->date(),
                TextColumn::make('unitAlat.serialNumber.serial_number')
                    ->label('Nomor Serial'),
                TextColumn::make('unitAlat.kondisi')
                    ->label('Kondisi Alat')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        default => strtoupper($state),
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('peminjaman.status')
                    ->label('Status Peminjaman')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        'returned' => 'success',
                        'overdue' => 'red',
                        default => 'secondary',
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => $user->hasRole('admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $user->hasRole('admin')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDetailPeminjamanAlats::route('/'),
            'create' => Pages\CreateDetailPeminjamanAlat::route('/create'),
            'view' => Pages\ViewDetailPeminjamanAlat::route('/{record}'),
            'edit' => Pages\EditDetailPeminjamanAlat::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return ($user->hasRole('admin') || ($user->hasRole('kepala')));
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return ($user->hasRole('admin') || ($user->hasRole('kepala')));
    }
}
