<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailPeminjamanPetaResource\Pages;
use App\Filament\Resources\DetailPeminjamanPetaResource\RelationManagers;
use App\Models\DetailPeminjamanPeta;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailPeminjamanPetaResource extends Resource
{
    protected static ?string $model = DetailPeminjamanPeta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'detail-peminjaman-peta';

    protected static ?string $modelLabel = 'Detail Peminjaman Peta';
    protected static ?string $pluralModelLabel = 'Detail Peminjaman Peta';

    public static function form(Form $form): Form
    {
        return $form->schema(function () use ($form) {
            $operation = $form->getOperation();

            $isView = $operation === 'view';
            $isEdit = $operation === 'edit';
            $isCreate = $operation === 'create';

            return array_filter([
                // PILIH PEMINJAMAN
                Select::make('id_peminjaman')
                    ->label('Nama Peminjam')
                    ->options(
                        fn() =>
                        \App\Models\Peminjaman::with('user')
                            ->get()
                            ->pluck('user.nama', 'id')
                    )
                    ->searchable()
                    ->disabled($isView)
                    ->required(),

                // PILIH UNIT PETA
                Select::make('id_unit_peta')
                    ->label('Nama Peta & Lokasi')
                    ->options(
                        \App\Models\UnitPeta::with('peta')->get()
                            ->mapWithKeys(fn($unit) => [
                                $unit->id => ($unit->peta->nama ?? 'Peta -') . ' | ' . ($unit->lokasi ?? 'Lokasi -')
                            ])
                    )
                    ->searchable()
                    ->disabled($isView)
                    ->required(),

                // VIEW-ONLY: NAMA PETA
                $isView
                    ? Placeholder::make('nama_peta')
                    ->label('Nama Peta')
                    ->content(fn($record) => optional($record?->unitPeta?->peta)->nama ?? '-')
                    : null,

                // VIEW-ONLY: LOKASI
                $isView
                    ? Placeholder::make('lokasi_peta')
                    ->label('Lokasi Penyimpanan')
                    ->content(fn($record) => optional($record?->unitPeta)->lokasi ?? '-')
                    : null,

                // VIEW-ONLY: KONDISI
                $isView
                    ? Placeholder::make('kondisi_peta')
                    ->label('Kondisi Peta')
                    ->content(fn($record) => match (optional($record?->unitPeta)->kondisi) {
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        default => 'Tidak Diketahui',
                    })
                    : null,

                // VIEW-ONLY: STATUS PEMINJAMAN
                $isView
                    ? Placeholder::make('status_peminjaman')
                    ->label('Status Peminjaman')
                    ->content(fn($record) => match (optional($record?->peminjaman)->status) {
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        default => 'Tidak Diketahui',
                    })
                    : null,

                // VIEW-ONLY: TANGGAL
                $isView
                    ? Placeholder::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->content(
                        fn($record) =>
                        optional($record?->peminjaman)->tanggal_pinjam
                            ? \Carbon\Carbon::parse($record->peminjaman->tanggal_pinjam)->format('d-m-Y')
                            : '-'
                    )
                    : null,

                $isView
                    ? Placeholder::make('tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->content(
                        fn($record) =>
                        optional($record?->peminjaman)->tanggal_pengembalian
                            ? \Carbon\Carbon::parse($record->peminjaman->tanggal_pengembalian)->format('d-m-Y')
                            : '-'
                    )
                    : null,
            ]);
        });
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        return $table
            ->columns([
                TextColumn::make('peminjaman.user.nama')
                    ->label('Nama Peminjam')
                    ->searchable(),

                TextColumn::make('unitPeta.peta.nama')
                    ->label('Peta yang Dipinjam')
                    ->searchable(),

                TextColumn::make('peminjaman.tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date(),

                TextColumn::make('peminjaman.tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->date(),

                TextColumn::make('unitPeta.lokasi')
                    ->label('Lokasi Peta')
                    ->limit(30)
                    ->tooltip(fn(string $state) => $state),

                TextColumn::make('unitPeta.kondisi')
                    ->label('Kondisi Peta')
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
                    }),
            ])

            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDetailPeminjamanPetas::route('/'),
            'create' => Pages\CreateDetailPeminjamanPeta::route('/create'),
            'view' => Pages\ViewDetailPeminjamanPeta::route('/{record}'),
            'edit' => Pages\EditDetailPeminjamanPeta::route('/{record}/edit'),
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
