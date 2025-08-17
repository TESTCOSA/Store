<?php

namespace App\Mail;

use App\Models\StockOut;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewStockOutRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $stockOut;

    public function __construct(StockOut $stockOut)
    {
        $this->stockOut = $stockOut;
    }

    public function build()
    {
        return $this->subject('New Stock Out Request')
                    ->view('emails.new_stock_out_request')
                    ->with([
                        'stockOut' => $this->stockOut,
                        'tools' => $this->stockOut->outDetails,
                        'inspector' => $this->stockOut->requestBy->name ?? 'N/A',
                        'wo_id' => $this->stockOut->wo_id,
                        'requestUrl' => url('/app/stock-outs'),
                    ]);
    }
}
