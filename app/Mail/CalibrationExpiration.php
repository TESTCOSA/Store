<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CalibrationExpiration extends Mailable
{
    use Queueable, SerializesModels;

    public $calibrations;

    public function __construct($calibrations)
    {
        $this->calibrations = $calibrations;
    }

    public function build()
    {
        return $this->subject('Upcoming Calibration Expirations')
            ->view('emails.calibration-expiration');
    }
}
