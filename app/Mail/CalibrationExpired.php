<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CalibrationExpired extends Mailable
{


    public $calibrations;

    /**
     * Create a new message instance.
     */
    public function __construct($calibrations)
    {
        $this->calibrations = $calibrations;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Upcoming Calibration Expiry')
            ->view('emails.calibration_expired');

    }
}
