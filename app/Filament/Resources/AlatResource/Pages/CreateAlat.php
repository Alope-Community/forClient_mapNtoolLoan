<?php

namespace App\Filament\Resources\AlatResource\Pages;

use App\Filament\Resources\AlatResource;
use App\Models\Alat;
use App\Models\SerialNumber;
use App\Models\UnitAlat;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Contracts\View\View;


class CreateAlat extends Page implements Forms\Contracts\HasForms
{
    use InteractsWithForms;
    protected static string $resource = AlatResource::class;

    public int $currentStep = 1;

    public $alatData = [];
    public $serialNumbers = [];
    public $unitAlatData = [];

    public ?Alat $alat = null;

    public function mount(): void
    {
        $this->form->fill([
            'alatData' => $this->alatData,
            'serialNumbers' => $this->serialNumbers,
            'unitAlatData' => $this->unitAlatData,
        ]);
    }

    protected function getFormSchema(): array
    {
        return match ($this->currentStep) {
            1 => [
                Forms\Components\TextInput::make('alatData.nama')->required(),
                Forms\Components\TextInput::make('alatData.deskripsi')->required(),
            ],
            2 => [
                Forms\Components\Repeater::make('serialNumbers')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')->required(),
                        Forms\Components\Textarea::make('deskripsi')->required(),
                    ])
                    ->default([]),
            ],
            3 => [
                Forms\Components\Repeater::make('unitAlatData')
                    ->schema([
                        Forms\Components\Select::make('id_serial_number')
                            ->label('Nomor Serial')
                            ->options(SerialNumber::pluck('serial_number', 'id'))
                            ->required(),
                        Forms\Components\Select::make('kondisi')
                            ->options(['baik' => 'Baik', 'rusak' => 'Rusak'])->required(),
                        Forms\Components\Textarea::make('lokasi')->required(),
                        Forms\Components\Radio::make('is_dipinjam')
                            ->options([0 => 'Sedang Dipinjam', 1 => 'Tersedia'])
                            ->inline()->required(),
                    ])
                    ->default([]),
            ],
            default => [],
        };
    }

    public function next(): void
    {
        $data = $this->form->getState();

        if ($this->currentStep === 1) {
            $this->alatData = $data['alatData'];
            $this->alat = Alat::create($this->alatData);
        }

        if ($this->currentStep === 2) {
            $this->serialNumbers = $data['serialNumbers'] ?? [];
            foreach ($this->serialNumbers as $serial) {
                SerialNumber::firstOrCreate(
                    ['serial_number' => $serial['serial_number']],
                    ['deskripsi' => $serial['deskripsi']]
                );
            }
        }

        $this->currentStep++;
        $this->form->fill(); // perbarui form sesuai step
    }

    public function previous(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->form->fill();
        }
    }

    public function submit(): void
    {
        foreach ($this->unitAlatData as $unit) {
            UnitAlat::create([
                'id_alat' => $this->alat->id,
                'id_serial_number' => $unit['id_serial_number'],
                'kondisi' => $unit['kondisi'],
                'lokasi' => $unit['lokasi'],
                'is_dipinjam' => $unit['is_dipinjam'],
            ]);
        }

        $this->redirect(AlatResource::getUrl('index'));
    }

    public function render(): View
    {
        return view('filament.resources.alat-resource.pages.create-alat');
    }

    public static function getResource(): string
    {
        return AlatResource::class;
    }
}
