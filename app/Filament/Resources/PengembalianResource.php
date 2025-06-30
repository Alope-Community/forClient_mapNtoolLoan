<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Filament\Resources\PengembalianResource\RelationManagers;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengembalianResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $modelLabel = 'Pengembalian';
    protected static ?string $pluralModelLabel = 'Pengembalian';
    protected static ?string $navigationLabel = 'Pengembalian';
    protected static ?string $slug = 'pengembalian';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-down';

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->schema([
            $user->hasRole('admin')
                ? Select::make('id_peminjam')
                ->relationship('user', 'nama')
                ->label('Dari Peminjam')
                ->searchable()
                ->preload()
                ->disabled()
                ->required()
                : Hidden::make('id_peminjam')
                ->default($user->id),

            DateTimePicker::make('tanggal_pinjam')
                ->label('Tanggal Pinjam')
                ->seconds(false)
                ->readOnly()
                ->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')
                ->native(false)
                ->required(),

            DateTimePicker::make('tanggal_pengembalian')
                ->label('Tanggal Pengembalian')
                ->seconds(false)
                ->readOnly()
                ->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')
                ->native(false)
                ->required(),

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
                        ->required(fn($get) => count($get('detailPeminjamanPeta') ?? []) === 0)
                        ->preload()
                        ->reactive()
                        ->columnSpan(6)
                ])
                ->addActionLabel('Tambah Alat')
                ->disabled()
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
                        ->required(fn($get) => count($get('detailPeminjamanAlat') ?? []) === 0)
                        ->preload()
                        ->reactive()
                        ->columnSpan(6),
                ])
                ->addActionLabel('Tambah Item')
                ->disabled()
                ->columns(1),

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

            FileUpload::make('bukti_pengembalian')
                ->preserveFilenames()
                ->downloadable()
                ->label('Upload Bukti Pengembalian')
                ->directory('pengembalian')
                ->loadingIndicatorPosition('right')
                ->removeUploadedFileButtonPosition('right')
                ->uploadButtonPosition('right')
                ->uploadProgressIndicatorPosition('right'),

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
                    'borrowed' => 'info1',
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengembalians::route('/'),
            'view' => Pages\ViewPengembalian::route('/{record}'),
            'edit' => Pages\EditPengembalian::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('status', 'pending');

        if (auth()->user()->hasRole('karyawan')) {
            $query->where('id_peminjam', auth()->id());
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
