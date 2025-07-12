<x-filament::page>
    <form wire:submit.prevent="{{ $currentStep === 3 ? 'submit' : 'next' }}">
        {{ $this->form }}

        <div class="mt-4 flex justify-between">
            @if ($currentStep > 1)
                <x-filament::button wire:click="previous" color="secondary">
                    Kembali
                </x-filament::button>
            @endif

            <x-filament::button type="submit">
                {{ $currentStep === 3 ? 'Simpan Semua' : 'Selanjutnya' }}
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
