<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SerialNumberResource\Pages;
use App\Filament\Resources\SerialNumberResource\RelationManagers;
use App\Models\SerialNumber;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SerialNumberResource extends Resource
{
    protected static ?string $model = SerialNumber::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $slug = 'serial-number';

    protected static ?string $modelLabel = 'Serial Number';
    protected static ?string $pluralModelLabel = 'Serial Number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('serial_number'),
                TextInput::make('deskripsi')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->columns([
                TextColumn::make('serial_number')
                    ->searchable(),
                TextColumn::make('deskripsi')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->deskripsi),
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
            'index' => Pages\ListSerialNumbers::route('/'),
            'create' => Pages\CreateSerialNumber::route('/create'),
            'view' => Pages\ViewSerialNumber::route('/{record}'),
            'edit' => Pages\EditSerialNumber::route('/{record}/edit'),
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
