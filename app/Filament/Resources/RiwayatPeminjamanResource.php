<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatPeminjamanResource\Pages;
use App\Filament\Resources\RiwayatPeminjamanResource\RelationManagers;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatPeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $modelLabel = 'Riwayat Peminjaman';
    protected static ?string $pluralModelLabel = 'Riwayat Peminjaman';
    protected static ?string $navigationLabel = 'Riwayat Peminjaman';
    protected static ?string $slug = 'riwayat-peminjaman';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                ->required()
                : Hidden::make('id_peminjam')
                ->default($user->id),

            DateTimePicker::make('tanggal_pinjam')
                ->label('Tanggal Pinjam')
                ->seconds(false)
                ->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')
                ->native(false)
                ->required(),

            DateTimePicker::make('tanggal_pengembalian')
                ->label('Tanggal Pengembalian')
                ->seconds(false)
                ->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')
                ->native(false)
                ->required(),

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
                ->minItems(1)
                ->addActionLabel('Tambah Alat')
                ->required()
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
                        ->reactive()
                        ->columnSpan(6),
                ])
                ->minItems(1)
                ->addActionLabel('Tambah Item')
                ->required()
                ->columns(1),

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
        $user = auth()->user();

        return $table
            ->columns([
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => auth()->user()->hasRole('admin')),
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->hasRole('admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('admin')),
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
            'index' => Pages\ListRiwayatPeminjamen::route('/'),
            'view' => Pages\ViewRiwayatPeminjaman::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('karyawan')) {
            $query->where('id_peminjam', auth()->id())
                ->whereIn('status', ['returned', 'rejected', 'overdue']);
        }

        return $query;
    }


    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
