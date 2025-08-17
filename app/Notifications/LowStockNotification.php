<?php
namespace App\Notifications;


use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    protected $item;
    protected $quantity;

    /**
     * Create a new notification instance.
     */
    public function __construct($item, $quantity)
    {
        $this->item = $item;
        $this->quantity = $quantity;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Low Stock Alert')
            ->line("The item '{$this->item}' is low on stock.")
            ->line("Current quantity: {$this->quantity}")
            ->action('View Inventory', url('/public/app/stocks'))
            ->line('Please restock as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'item' => $this->item,
            'quantity' => $this->quantity,
        ];
    }
}

