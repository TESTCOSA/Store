<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\HtmlString;

class ExpiredCalibration extends Mailable
{
    use Queueable;

    protected $calibrations;

    /**
     * Create a new notification instance.
     */
    public function __construct($calibrations)
    {
        $this->calibrations = $calibrations;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $tableRows = '';
        foreach ($this->calibrations as $calibration) {
            $remainingDays = now()->diffInDays($calibration->due_date, false);
            $tableRows .= "
            <tr>
                <td>{$calibration->id}</td>
                <td>{$calibration->item->name}</td>
                <td>{$calibration->due_date}</td>
                <td>{$remainingDays} days</td>
            </tr>
        ";
        }

        $htmlTable = "
        <table style='width: 100%; border-collapse: collapse;'>
            <thead>
                <tr>
                    <th style='border: 1px solid #ddd; padding: 8px;'>ID</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Item Name</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Due Date</th>
                    <th style='border: 1px solid #ddd; padding: 8px;'>Remaining Days</th>
                </tr>
            </thead>
            <tbody>
                $tableRows
            </tbody>
        </table>
    ";
        return (new MailMessage)
            ->subject('Upcoming Calibration Expiry')
            ->line('The following calibrations are nearing expiry:')
            ->line(new \Illuminate\Support\HtmlString($htmlTable))
            ->action('View Details', url('/calibrations'))
            ->line('Thank you for using our application!');
    }
    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'calibrations' => $this->calibrations->map(function ($calibration) {
                return [
                    'id' => $calibration->id,
                    'item_name' => $calibration->item->name,
                    'due_date' => $calibration->due_date,
                    'remaining_days' => now()->diffInDays($calibration->due_date, false),
                ];
            }),
        ];
    }
}
