<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;

class ApprovedRequest extends Mailable
{

    public $record;

    /**
     * Create a new message instance.
     */
    public function __construct($record)
    {
        $this->record = $record;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->subject('Upcoming Calibration Expiry')
            ->view('emails.approved_request')
            ->with('details', $this->record->first()->outDetails);

    }

}
