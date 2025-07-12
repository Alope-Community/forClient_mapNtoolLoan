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
                    TextInput::make('serial_number')
                        ->label('Nomor Serial')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('deskripsi_serial')
                        ->label('Deskripsi Serial')
                        ->required()
                        ->columnSpanFull(),
                ])->afterValidation(function (Get $get, Set $set) {
                    $serial = SerialNumber::create([
                        'id_alat' => $get('id_alat'),
                        'serial_number' => $get('serial_number'),
                        'deskripsi' => $get('deskripsi_serial'),
                    ]);
                    $set('id_serial_number', $serial->id);
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
            ])->submitAction(new HtmlString(Blade::render(<<<BLADE
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
