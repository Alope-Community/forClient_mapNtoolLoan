<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
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

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form->schema([
            $user->hasRole('admin')
                ? Select::make('id_peminjam')
                ->relationship('user', 'nama')
                ->label('Dari Peminjam')
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

            Select::make('id_unit_alat')
                ->label('Pilih Unit Alat')
                ->multiple()
                ->searchable()
                ->options(
                    \App\Models\UnitAlat::with('alat')
                        ->where('is_dipinjam', false)
                        ->get()
                        ->mapWithKeys(fn($unit) => [
                            $unit->id => $unit->alat->nama . ' - ' . optional($unit->serialNumber)->serial_number,
                        ])
                )
                ->preload()
                ->required(fn($get) => empty($get('id_unit_peta'))),

            Select::make('id_unit_peta')
                ->label('Pilih Unit Peta')
                ->multiple()
                ->options(
                    \App\Models\UnitPeta::where('is_dipinjam', false)
                        ->get()
                        ->mapWithKeys(fn($unit) =>  [
                            $unit->id => $unit->peta->nama,
                        ])
                )
                ->preload()
                ->required(fn($get) => empty($get('id_unit_alat'))),

            ToggleButtons::make('status')
                ->label('Status')
                ->inline()
                ->required()
                ->visible($user->hasRole('admin'))
                ->icons([
                    'pending' => 'heroicon-o-clock',
                    'approved' => 'heroicon-o-check-circle',
                    'borrowed' => 'heroicon-o-arrow-down-circle',
                    'rejected' => 'heroicon-o-x-circle',
                    'returned' => 'heroicon-o-arrow-left-circle',
                    'overdue' => 'heroicon-o-exclamation-circle',
                ])
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'borrowed' => 'Borrowed',
                    'rejected' => 'Rejected',
                    'returned' => 'Returned',
                    'overdue' => 'Overdue',
                ])
                ->colors([
                    'pending' => 'warning',
                    'approved' => 'success',
                    'borrowed' => 'info',
                    'rejected' => 'danger',
                    'returned' => 'success',
                    'overdue' => 'danger',
                ]),
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
            $query->where('id_peminjam', auth()->id());
        }

        return $query;
    }
}
