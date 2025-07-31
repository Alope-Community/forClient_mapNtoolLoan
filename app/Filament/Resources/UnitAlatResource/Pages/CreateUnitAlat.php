<?php

namespace App\Filament\Resources\UnitAlatResource\Pages;

use App\Filament\Resources\UnitAlatResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use App\Models\Alat;
use App\Models\SerialNumber;
use App\Models\UnitAlat;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CreateUnitAlat extends CreateRecord
{
    protected static string $resource = UnitAlatResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('id_alat')->reactive(),
            Hidden::make('id_serial_number')->reactive(),

            Wizard::make([
                Step::make('Informasi Alat')->schema([
                    TextInput::make('nama')
                        ->label('Nama Alat')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('deskripsi')
                        ->label('Deskripsi Alat')
                        ->required()
                        ->columnSpanFull(),
                ])->afterValidation(function (Get $get, Set $set) {
                    $alat = Alat::create([
                        'nama' => $get('nama'),
                        'deskripsi' => $get('deskripsi'),
                    ]);
                    $set('id_alat', $alat->id);
                }),

                Step::make('Nomor Serial')->schema([
                    Radio::make('serial_mode')
                        ->label('Mode Input Serial Number')
                        ->options([
                            'baru' => 'Tambah Serial Number Baru',
                            'pilih' => 'Pilih dari Serial Number yang Ada',
                        ])
                        ->default('baru')
                        ->inline()
                        ->required()
                        ->reactive(),

                    TextInput::make('serial_number')
                        ->label('Nomor Serial Baru')
                        ->required(fn(Get $get) => $get('serial_mode') === 'baru')
                        ->visible(fn(Get $get) => $get('serial_mode') === 'baru')
                        ->columnSpanFull(),

                    Textarea::make('deskripsi')
                        ->label('Deskripsi Serial')
                        ->required(fn(Get $get) => $get('serial_mode') === 'baru')
                        ->visible(fn(Get $get) => $get('serial_mode') === 'baru')
                        ->columnSpanFull(),

                    Select::make('id_serial_number')
                        ->label('Pilih Nomor Serial')
                        ->options(SerialNumber::all()->pluck('serial_number', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(fn(Get $get) => $get('serial_mode') === 'pilih')
                        ->visible(fn(Get $get) => $get('serial_mode') === 'pilih')
                        ->reactive(),

                    Textarea::make('deskripsi_terpilih')
                        ->label('Deskripsi Serial (Terpilih)')
                        ->disabled()
                        ->visible(fn(Get $get) => $get('serial_mode') === 'pilih')
                        ->afterStateHydrated(function (Set $set, Get $get) {
                            $serial = SerialNumber::find($get('id_serial_number'));
                            if ($serial) {
                                $set('deskripsi_serial_terpilih', $serial->deskripsi);
                            }
                        })
                        ->reactive()
                        ->columnSpanFull(),
                ])->afterValidation(function (Get $get, Set $set) {
                    if ($get('serial_mode') === 'baru') {
                        $serial = SerialNumber::create([
                            'id_alat' => $get('id_alat'),
                            'serial_number' => $get('serial_number'),
                            'deskripsi' => $get('deskripsi'),
                        ]);
                        $set('id_serial_number', $serial->id);
                    }
                }),

                Step::make('Detail Unit Alat')->schema([
                    Select::make('kondisi')
                        ->label('Kondisi')
                        ->options([
                            'baik' => 'Baik',
                            'rusak' => 'Rusak',
                        ])
                        ->required(),

                    Textarea::make('lokasi')
                        ->label('Lokasi Penyimpanan')
                        ->rows(2)
                        ->required(),

                    Radio::make('is_dipinjam')
                        ->label('Status Alat')
                        ->options([
                            1 => 'Tersedia',
                            0 => 'Sedang Dipinjam',
                        ])
                        ->inline()
                        ->required(),
                ]),
            ])->columnSpanFull()
            ->submitAction(new HtmlString(Blade::render(<<<BLADE
            <x-filament::button type="submit" size="sm">
                Submit
            </x-filament::button>
            BLADE)))
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            'id_alat' => $data['id_alat'],
            'id_serial_number' => $data['id_serial_number'],
            'kondisi' => $data['kondisi'],
            'lokasi' => $data['lokasi'],
            'is_dipinjam' => $data['is_dipinjam'],
        ];
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return UnitAlat::create($data);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
        ];
    }
}
