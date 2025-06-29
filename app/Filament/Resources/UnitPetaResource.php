<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitPetaResource\Pages;
use App\Filament\Resources\UnitPetaResource\RelationManagers;
use App\Models\UnitPeta;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitPetaResource extends Resource
{
    protected static ?string $model = UnitPeta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_peta')
                    ->label('Peta')
                    ->relationship('peta', 'nama')
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
                    ->required(),

                Radio::make('is_dipinjam')
                    ->label('Status Peminjaman')
                    ->options([
                        0 => 'Sedang Dipinjam',
                        1 => 'Tersedia',
                    ])
                    ->inline()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peta.nama') // asumsi field 'nama' di tabel 'peta'
                    ->label('Nama Peta')
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
                    ->limit(30)
                    ->tooltip(fn(string $state): string => $state), // untuk lihat lokasi lengkap saat hover

                TextColumn::make('is_dipinjam')
                    ->label('Status')
                    ->formatStateUsing(fn(bool $state) => $state ? 'Tersedia' : 'Sedang Dipinjam')
                    ->color(fn(bool $state) => $state ? 'success' : 'danger')
                    ->icon(fn(bool $state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->iconPosition(IconPosition::Before)
                    ->badge(),
            ])

            ->filters([
                //
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
            'index' => Pages\ListUnitPetas::route('/'),
            'create' => Pages\CreateUnitPeta::route('/create'),
            'view' => Pages\ViewUnitPeta::route('/{record}'),
            'edit' => Pages\EditUnitPeta::route('/{record}/edit'),
        ];
    }
}
