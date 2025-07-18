<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitAlatResource\Pages;
use App\Models\UnitAlat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class UnitAlatResource extends Resource
{
    protected static ?string $model = UnitAlat::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $slug = 'unit-alat';
    protected static ?string $modelLabel = 'Unit Alat';
    protected static ?string $pluralModelLabel = 'Unit Alat';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('id_alat')
                ->label('Nama Alat')
                ->relationship('alat', 'nama')
                ->searchable()
                ->required(),

            Select::make('id_serial_number')
                ->label('Nomor Serial')
                ->relationship('serialNumber', 'serial_number')
                ->searchable()
                ->required(),

            Select::make('kondisi')
                ->label('Kondisi')
                ->options([
                    'baik' => 'Baik',
                    'rusak' => 'Rusak',
                ])
                ->required(),

            Textarea::make('lokasi')
                ->label('Lokasi Penyimpanan')
                ->placeholder('Contoh: Gudang Utama, Rak 3')
                ->rows(2)
                ->required(),

            Radio::make('is_dipinjam')
                ->label('Status Alat')
                ->options([
                    0 => 'Tersedia',
                    1 => 'Sedang Dipinjam',
                ])
                ->inline()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('alat.nama')
                    ->label('Nama Alat')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('serialNumber.serial_number')
                    ->label('Nomor Serial')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kondisi')
                    ->label('Kondisi')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->color(fn(string $state) => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'danger',
                    }),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->limit(30),

                TextColumn::make('is_dipinjam')
                    ->label('Status')
                    ->formatStateUsing(fn(bool $state) => $state ? 'Sedang Dipinjam' : 'Tersedia')
                    ->color(fn(bool $state) => $state ? 'warning' : 'success')
                    ->icon(fn(bool $state) => $state ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                    ->iconPosition(IconPosition::Before)
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitAlats::route('/'),
            'create' => Pages\CreateUnitAlat::route('/create'),
            'view' => Pages\ViewUnitAlat::route('/{record}'),
            'edit' => Pages\EditUnitAlat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['alat', 'serialNumber']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user->hasRole('admin') || $user->hasRole('kepala');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user->hasRole('admin') || $user->hasRole('kepala');
    }
}
