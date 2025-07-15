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
                        ->label('File PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('file-peta')
                        ->visibility('public')
                        ->required()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->reactive()
                        ->preserveFilenames()
                        ->maxSize(10240) // 10MB
                        ->columnSpanFull(),
                ])->afterValidation(function (Get $get, Set $set) {
                    $gambarInput = $get('gambar');

                    // Tangani single / multiple upload
                    $gambarFile = is_array($gambarInput) ? ($gambarInput[0] ?? null) : $gambarInput;

                    // Cek jika tidak ada file
                    if (!$gambarFile || !($gambarFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)) {
                        throw new \Exception("File PDF tidak ditemukan atau belum dipilih.");
                    }

                    // Simpan file ke storage permanen (misal di public/file-peta)
                    $savedPath = $gambarFile->store('file-peta', 'public'); // folder 'storage/app/public/file-peta'

                    // Simpan ke DB
                    $peta = \App\Models\Peta::create([
                        'nama' => $get('nama'),
                        'deskripsi' => $get('deskripsi'),
                        'nomor' => $get('nomor'),
                        'kabupaten' => $get('kabupaten'),
                        'provinsi' => $get('provinsi'),
                        'gambar' => $savedPath, // Simpan path hasil upload
                    ]);

                    // Simpan ID untuk step selanjutnya
                    $set('id_peta', $peta->id);
                }),

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
                            1 => 'Tersedia',
                            0 => 'Sedang Dipinjam',
                        ])
                        ->inline()
                        ->required(),
                ]),
            ])->columnSpanFull()
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            'id_peta' => $data['id_peta'],
            'kondisi' => $data['kondisi'],
            'lokasi' => $data['lokasi'],
            'is_dipinjam' => $data['is_dipinjam'],
        ];
    }
}
