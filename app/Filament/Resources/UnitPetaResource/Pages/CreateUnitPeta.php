<?php

namespace App\Filament\Resources\UnitPetaResource\Pages;

use App\Filament\Resources\UnitPetaResource;
use App\Models\Peta;
use App\Models\UnitPeta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\{Wizard, Wizard\Step, TextInput, Textarea, Select, Radio, Hidden, FileUpload};

class CreateUnitPeta extends CreateRecord
{
    protected static string $resource = UnitPetaResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('id_peta')->reactive(),

            Wizard::make([
                Step::make('Informasi Peta')->schema([
                    TextInput::make('nama')
                        ->label('Nama Peta')
                        ->required(),

                    Textarea::make('deskripsi')
                        ->label('Deskripsi Peta'),

                    TextInput::make('nomor')
                        ->label('Nomor'),

                    TextInput::make('kabupaten')
                        ->label('Kabupaten')
                        ->required(),

                    TextInput::make('provinsi')
                        ->label('Provinsi')
                        ->required(),

                    FileUpload::make('gambar')
                        ->required()
                        ->previewable()
                        ->acceptedFileTypes(['application/pdf'])
                        ->openable()
                        ->downloadable()
                        ->label('Upload Gambar Peta')
                        ->loadingIndicatorPosition('right')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('right')
                        ->uploadProgressIndicatorPosition('right')
                        ->disk('public')
                        ->directory('peta')
                        ->preserveFilenames(),
                ]),

                Step::make('Detail Unit Peta')->schema([
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
                            0 => 'Tersedia',
                            1 => 'Sedang Dipinjam',
                        ])
                        ->inline()
                        ->required(),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    protected function handleRecordCreation(array $data): UnitPeta
    {
        // Simpan data ke model Peta terlebih dahulu
        $peta = Peta::create([
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'nomor' => $data['nomor'] ?? null,
            'kabupaten' => $data['kabupaten'],
            'provinsi' => $data['provinsi'],
            'gambar' => $data['gambar'],
        ]);

        // Simpan UnitPeta dengan referensi ke ID Peta
        $unitPeta = UnitPeta::create([
            'id_peta' => $peta->id,
            'kondisi' => $data['kondisi'],
            'lokasi' => $data['lokasi'],
            'is_dipinjam' => $data['is_dipinjam'],
        ]);

        return $unitPeta;
    }
}
