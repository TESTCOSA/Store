<?php

namespace App\Filament\Resources\TravelingBlockResource\Pages;

use App\Filament\Resources\TravelingBlockResource;
use App\Models\TravelingBlock\Main as TravelingBlock;
use App\Services\TravelingBlockCertificateService;
use Filament\Pages\Page;

class PrintDraftCertification extends Page
{
    protected static string $resource = TravelingBlockResource::class;

    public function mount(TravelingBlock $record, TravelingBlockCertificateService $service)
    {
        $pdf = $service->generatePdf($record, true);
        response()->streamDownload(fn() => print($pdf), 'CERT-'.$record->id.'-DRAFT.pdf')->send();
        exit;
    }
}

