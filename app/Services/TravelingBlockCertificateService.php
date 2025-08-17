<?php

namespace App\Services;

use App\Models\TravelingBlock\Main as TravelingBlock;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class TravelingBlockCertificateService
{
    /**
     * Generate the PDF certificate using mPDF.
     */
    public function generatePdf(TravelingBlock $record, bool $isDraft = false): string
    {
        $record->load([
            'workOrder',
            'readings',
            'checklist',
            'photos',
            'approver',
            'inspector',
        ]);

        $data = [
            'record' => $record,
            'isDraft' => $isDraft,
            'cert_name' => "TB-{$record->inspector?->userDetails?->emp_code}-{$record->wo_id}-" . str_pad($record->sequence, 4, '0', STR_PAD_LEFT),
        ];

        $html = View::make('pdf.traveling-block-certificate', $data)->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
        ]);

        if ($isDraft) {
            $mpdf->SetWatermarkText('DRAFT', 0.1);
            $mpdf->showWatermarkText = true;
        }

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }
}

