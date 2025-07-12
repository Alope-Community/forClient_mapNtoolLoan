<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlatResource\Pages;
use App\Filament\Resources\AlatResource\RelationManagers;
use App\Models\Alat;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlatResource extends Resource
{
    protected static ?string $model = Alat::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $slug = 'alat';

    protected static ?string $modelLabel = 'Alat';
    protected static ?string $pluralModelLabel = 'Alat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Data Alat')->schema([
                        TextInput::make('nama')->required(),
                        TextInput::make('deskripsi')->required(),
                    ]),
                    Step::make('Serial Number (opsional)')->schema([
                        Repeater::make('serialNumber')
                            ->schema([
                                TextInput::make('serial_number')->required(),
                                Textarea::make('deskripsi')->required(),
                            ])
                            ->default([])
                            ->dehydrated(false),
                    ]),
                    Step::make('Unit Alat')->schema([
                        Repeater::make('unitAlat')
                            ->schema([
                                Select::make('id_serial_number')
                                    ->options(\App\Models\SerialNumber::pluck('serial_number', 'id'))
                                    ->required(),
                                Select::make('kondisi')
                                    ->options(['baik' => 'Baik', 'rusak' => 'Rusak'])
                                    ->required(),
                                Textarea::make('lokasi')->required(),
                                Radio::make('is_dipinjam')
                                    ->options([0 => 'Sedang Dipinjam', 1 => 'Tersedia'])
                                    ->required(),
                            ])
                    ])
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->columns([
                TextColumn::make('nama')->searchable(),
                TextColumn::make('deskripsi')
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListAlats::route('/'), // route tetap / karena sudah diganti ke 'alat' melalui $slug
            'create' => Pages\CreateAlat::route('/create'),
            'edit' => Pages\EditAlat::route('/{record}/edit'),
        ];
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

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
