<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitPetaResource\Pages;
use App\Filament\Resources\UnitPetaResource\RelationManagers;
use App\Models\UnitPeta;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
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
use Illuminate\Support\HtmlString;

class UnitPetaResource extends Resource
{
    protected static ?string $model = UnitPeta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'unit-peta';

    protected static ?string $modelLabel = 'Unit Peta';
    protected static ?string $pluralModelLabel = 'Unit Peta';

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

                Radio::make('is_dipinjam')
                    ->label('Status Peminjaman')
                    ->options([
                        1 => 'Sedang Dipinjam',
                        0 => 'Tersedia',
                    ])
                    ->inline()
                    ->required(),

                Textarea::make('lokasi')
                    ->label('Lokasi Penyimpanan')
                    ->columnSpanFull()
                    ->required(),

                FileUpload::make('gambar_peta')
                    ->label('Gambar Peta')
                    ->disk('public')
                    ->directory('peta')
                    ->preserveFilenames()
                    ->openable()
                    ->downloadable()
                    ->visibility('public')
                    ->reactive()
                    ->visible(fn($get) => !empty($get('id_peta')))
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        return $file->getClientOriginalName();
                    }),

                Placeholder::make('preview_gambar')
                    ->label('Gambar Saat Ini')
                    ->content(function ($get) {
                        $peta = \App\Models\Peta::find($get('id_peta'));
                        return $peta && $peta->gambar
                            ? new HtmlString('<a href="' . asset('storage/' . $peta->gambar) . '" style="text-decoration:none;" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" target="_blank" rel="noopener noreferrer">Lihat File</a>')
                            : 'Tidak ada gambar';
                    })
                    ->visible(fn($get) => !empty($get('id_peta')))
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

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
                    ->formatStateUsing(fn(bool $state) => $state ? 'Sedang Dipinjam' : 'Tersedia')
                    ->color(fn(bool $state) => $state ? 'warning' : 'success')
                    ->icon(fn(bool $state) => $state ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                    ->iconPosition(IconPosition::Before)
                    ->badge(),
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
            'index' => Pages\ListUnitPetas::route('/'),
            'create' => Pages\CreateUnitPeta::route('/create'),
            'view' => Pages\ViewUnitPeta::route('/{record}'),
            'edit' => Pages\EditUnitPeta::route('/{record}/edit'),
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
