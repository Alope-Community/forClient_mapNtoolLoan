<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?string $slug = 'peminjaman';

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->schema([
            $user->hasRole('admin')
                ? Select::make('id_peminjam')
                ->options(
                    \App\Models\User::role('karyawan')
                        ->pluck('nama', 'id') // ['id' => 'nama']
                )
                ->label('Dari Peminjam')
                ->searchable()
                ->preload()
                ->required()
                : Hidden::make('id_peminjam')
                ->default($user->id),

            DateTimePicker::make('tanggal_pinjam')
                ->label('Tanggal Pinjam')
                ->seconds(false)
                ->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')
                ->native(false)
                ->reactive()
                ->closeOnDateSelection()
                ->required(),

            DateTimePicker::make('tanggal_pengembalian')
                ->label('Tanggal Pengembalian')
                ->seconds(false)
                ->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')
                ->native(false)
                ->closeOnDateSelection()
                ->required()
                ->reactive()
                ->after('tanggal_pinjam'),

            // Select::make('id_unit_alat')
            //     ->label('Pilih Unit Alat')
            //     ->multiple()
            //     ->searchable()
            //     ->options(
            //         \App\Models\UnitAlat::with('alat')
            //             ->where('is_dipinjam', false)
            //             ->get()
            //             ->mapWithKeys(fn($unit) => [
            //                 $unit->id => $unit->alat->nama . ' - ' . optional($unit->serialNumber)->serial_number,
            //             ])
            //     )
            //     ->preload()
            //     ->required(fn($get) => empty($get('id_unit_peta'))),

            // Select::make('id_unit_peta')
            //     ->label('Pilih Unit Peta')
            //     ->multiple()
            //     ->options(
            //         \App\Models\UnitPeta::where('is_dipinjam', false)
            //             ->get()
            //             ->mapWithKeys(fn($unit) =>  [
            //                 $unit->id => $unit->peta->nama,
            //             ])
            //     )
            //     ->preload()
            //     ->required(fn($get) => empty($get('id_unit_alat'))),

            // ->options(
            //     \App\Models\UnitAlat::with('alat', 'serialNumber')
            //         ->where('is_dipinjam', false)
            //         ->get()
            //         ->pluck(
            //             fn($unit) => $unit->alat->nama . ' - ' . optional($unit->serialNumber)->serial_number,
            //             'id'
            //         )
            // )

            Repeater::make('detailPeminjamanAlat')
                ->label('Peminjaman Alat')
                ->relationship()
                ->live()
                ->schema([
                    Select::make('id_unit_alat')
                        ->label('Pilih Unit Alat')
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->options(function ($get) {
                            $selectedId = $get('id_unit_alat');

                            $query = \App\Models\UnitAlat::with('alat')
                                ->where('is_dipinjam', false);

                            // Jika ada yang sudah dipilih (misalnya saat edit), tampilkan juga
                            if ($selectedId) {
                                $query->orWhere('id', $selectedId);
                            }

                            return $query->get()
                                ->mapWithKeys(fn($unit) => [
                                    $unit->id => $unit->alat->nama,
                                ]);
                        })
                        ->getOptionLabelFromRecordUsing(
                            fn($record) => optional($record->alat)->nama ?? 'Tidak ditemukan'
                        )
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->columnSpan(6)
                ])
                ->defaultItems(0)
                ->addActionLabel('Tambah Alat')
                ->requiredWithout('detailPeminjamanPeta')
                ->columns(1),

            Repeater::make('detailPeminjamanPeta')
                ->label('Peminjaman Peta')
                ->relationship()
                ->live()
                ->schema([
                    Select::make('id_unit_peta')
                        ->label('Pilih Unit Peta')
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->options(function ($get) {
                            $selectedId = $get('id_unit_peta');

                            $query = \App\Models\UnitPeta::with('peta')
                                ->where('is_dipinjam', false);

                            // Jika ada yang sudah dipilih (misalnya saat edit), tampilkan juga
                            if ($selectedId) {
                                $query->orWhere('id', $selectedId);
                            }

                            return $query->get()
                                ->mapWithKeys(fn($unit) => [
                                    $unit->id => $unit->peta->nama,
                                ]);
                        })
                        ->getOptionLabelFromRecordUsing(
                            fn($record) =>
                            $record->peta->nama // ubah sesuai kolom yang kamu butuhkan
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->columnSpan(6),
                ])
                ->defaultItems(0)
                ->addActionLabel('Tambah Item')
                ->requiredWithout('detailPeminjamanAlat')
                ->columns(1),

            ToggleButtons::make('status')
                ->inline()
                ->visibleOn('edit')
                ->options([
                    'pending' => 'Menunggu',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                ])
                ->icons([
                    'pending' => 'heroicon-o-clock',
                    'approved' => 'heroicon-o-check-circle',
                    'rejected' => 'heroicon-o-x-circle',
                ])
                ->colors([
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                ])

            // Repeater::make('detailPeminjamanPeta')
            //     ->label('Peminjaman Peta')
            //     ->schema([
            //         Group::make([
            //             Select::make('id_unit_peta')
            //                 ->label('Pilih Unit Peta')
            //                 ->disableOptionsWhenSelectedInSiblingRepeaterItems()
            //                 ->options(
            //                     \App\Models\UnitPeta::with('peta')
            //                         ->where('is_dipinjam', false)
            //                         ->get()
            //                         ->mapWithKeys(fn($unit) => [
            //                             $unit->id => $unit->peta->nama,
            //                         ])
            //                 )
            //                 ->searchable()
            //                 ->preload()
            //                 ->reactive()
            //                 ->columnSpan(6),
            //         ]),
            //     ])
            //     ->minItems(1)
            //     ->addActionLabel('Tambah Item')
            //     ->required()
            //     ->columns(1)

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.nama')
                ->label('Nama Peminjam')
                ->searchable()
                ->sortable(),

            TextColumn::make('tanggal_pinjam')
                ->label('Tanggal Pinjam')
                ->dateTime('d M Y H:i')
                ->sortable(),

            TextColumn::make('tanggal_pengembalian')
                ->label('Tanggal Pengembalian')
                ->dateTime('d M Y H:i')
                ->sortable(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->sortable()
                ->color(fn(string $state): string => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'returned' => 'success',
                    'rejected' => 'danger',
                    'overdue' => 'danger',
                    default => 'secondary',
                }),
        ])
            ->filters([
                // Tambahkan filter jika dibutuhkan
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan RelationManagers jika ada
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'view' => Pages\ViewPeminjaman::route('/{record}'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('karyawan')) {
            $query->where('id_peminjam', auth()->id())->where('status', 'pending');
        }

        return $query;
    }
}
