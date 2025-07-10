<?php

namespace App\Filament\Resources\PengembalianResource\Pages;

use App\Filament\Resources\PengembalianResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPengembalian extends ViewRecord
{
    protected static string $resource = PengembalianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('downloadPDF'),
        ];
    }

    public function downloadPDF()
    {
        $record = $this->record;

        $pdf = Pdf::loadView('pdf.pengembalian', ['record' => $record]);

        return response()->streamDownload(
            fn() => print($pdf->stream()),
            'pengembalian_' . $record->id . '.pdf'
        );
    }
}
