<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetaResource\Pages;
use Illuminate\Support\Facades\Storage;
use App\Models\Peta;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;

class PetaResource extends Resource
{
    protected static ?string $model = Peta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'peta';

    protected static ?string $modelLabel = 'Peta';
    protected static ?string $pluralModelLabel = 'Peta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')->required(),
                Textarea::make('deskripsi'),
                TextInput::make('nomor'),
                TextInput::make('kabupaten')->required(),
                TextInput::make('provinsi')->required(),
                FileUpload::make('gambar')
                    ->label('File PDF')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('file-peta')
                    ->required()
                    ->previewable()
                    ->downloadable()
                    ->openable()
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        return $table
            ->columns([
                TextColumn::make('nama')->sortable()->searchable(),
                TextColumn::make('provinsi')->sortable()->searchable(),
                TextColumn::make('gambar')
                    ->label('File PDF')
                    ->formatStateUsing(function ($state, $record) {
                        $fileUrl = Storage::url($record->gambar);
                        return new HtmlString(
                            '<a href="' . $fileUrl . '" target="_blank" title="Buka PDF" style="margin-right:8px;">📄</a>' .
                                '<a href="' . $fileUrl . '" download title="Unduh PDF">⬇️</a>'
                        );
                    })
                    ->html(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime(),
            ])
            ->filters([])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
                \Filament\Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->hasRole('admin')),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListPetas::route('/'),
            'create' => Pages\CreatePeta::route('/create'),
            'view' => Pages\ViewPeta::route('/{record}'),
            'edit' => Pages\EditPeta::route('/{record}/edit'),
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
