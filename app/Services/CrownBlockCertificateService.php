<?php

namespace App\Services;

use App\Models\CrownBlock\Main as CrownBlock;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class CrownBlockCertificateService
{
    /**
     * Generate the PDF certificate using mPDF.
     *
     * @param CrownBlock $record The certificate record.
     * @param boolean $isDraft Determines if a "DRAFT" watermark should be applied.
     * @return string The raw PDF data.
     */
    public function generatePdf(CrownBlock $record, bool $isDraft = false): string
    {
        // Eager load all necessary relationships
        $record->load([
            'workOrder',
            'clusterReadings',
            'fastLineReading',
            'checklist',
            'photos',
            'approver',
            'inspector',
            'checklist.details.item',
        ]);

        // Create the data payload for the Blade view
        $data = [
            'record' => $record,
            'isDraft' => $isDraft,
            'cert_name' => "TEST-{$record->inspector?->userDetails?->emp_code}-{$record->wo_id}-" . str_pad($record->sequence, 4, '0', STR_PAD_LEFT),
        ];

        // Render the Blade view to an HTML string
        $html = View::make('pdf.crown-block-certificate', $data)->render();

        // Initialize mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
        ]);

        // Set a watermark if it's a draft
        if ($isDraft) {
            $mpdf->SetWatermarkText('DRAFT', 0.1); // Set text and transparency
            $mpdf->showWatermarkText = true;
        }

        // Write the HTML to the PDF
        $mpdf->WriteHTML($html);

        // Return the PDF content as a string
        return $mpdf->Output('', Destination::STRING_RETURN);
    }
}
