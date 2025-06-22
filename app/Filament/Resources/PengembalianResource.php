<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengembalianResource\Pages;
use App\Filament\Resources\PengembalianResource\RelationManagers;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
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
        $operation = $form->getOperation();

        return $form->schema([
            Placeholder::make('id_peminjam')
                ->label('Nama Peminjam')
                ->content(fn($record) => optional($record?->user)->nama ?? '-'),

            Placeholder::make('tanggal_pinjam')
                ->label('Tanggal Pinjam')
                ->content(
                    fn($record) =>
                    optional($record?->tanggal_pinjam)
                        ? \Carbon\Carbon::parse($record->tanggal_pinjam)->format('d-m-Y')
                        : '-'
                ),

            Placeholder::make('tanggal_pengembalian')
                ->label('Tanggal Pengembalian')
                ->content(
                    fn($record) =>
                    optional($record?->tanggal_pengembalian)
                        ? \Carbon\Carbon::parse($record->tanggal_pengembalian)->format('d-m-Y')
                        : '-'
                ),

            Select::make('status')
                ->label('Status Peminjaman')
                ->options([
                    'pending' => 'Menunggu Persetujuan',
                    'approved' => 'Disetujui',
                    'borrowed' => 'Sedang Dipinjam',
                    'rejected' => 'Ditolak',
                    'returned' => 'Dikembalikan',
                    'overdue' => 'Terlambat',
                ])
                ->required()
                ->disabled(fn() => $operation === 'view'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.nama')
                    ->label('Nama Peminjam'),

                TextColumn::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->date(),

                TextColumn::make('tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->date(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'borrowed' => 'Sedang Dipinjam',
                        'rejected' => 'Ditolak',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'info',
                        'borrowed' => 'warning',
                        'rejected' => 'danger',
                        'returned' => 'success',
                        'overdue' => 'red',
                        default => 'secondary',
                    }),
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
    
    public static function canCreate(): bool
    {
        return false; 
    }
}
