<?php

namespace App\Notifications;


use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CalibrationExpiryNotification extends Notification
{
    protected $calibration;

    public function __construct($calibration)
    {
        $this->calibration = $calibration;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Calibration for item {$this->calibration->item->name} is due on {$this->calibration->due_date}.",
            'item_id' => $this->calibration->item_id,
            'due_date' => $this->calibration->due_date,
        ];
    }
}
