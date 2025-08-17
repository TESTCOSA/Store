<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestApprovalNotification extends Notification
{
    protected $requestData;
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @param array $requestData The request data (e.g., request number, item details).
     * @param string $type The type of notification (e.g., 'new_request', 'approved').
     */
    public function __construct($requestData, $type)
    {
        $this->requestData = $requestData;
        $this->type = $type;
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
        if ($this->type === 'new_request') {
            // Notification for supervisor and store_keeper
            return (new MailMessage)
                ->subject('New Request for Approval')
                ->line('A new request has been submitted and requires your approval.')
                ->line("Request Number: {$this->requestData['request_number']}")
                ->line("Requested By: {$this->requestData['requested_by']}")
                ->line("Work Order ID: {$this->requestData['wo_id']}")
                ->line("Items Requested:")
                ->line($this->formatItems($this->requestData['items']))
                ->action('Approve Request', url('/app/stock-outs'), 'danger')
                ->line('Please review and approve the request as soon as possible.');
        } elseif ($this->type === 'approved') {
            // Notification for user
            return (new MailMessage)
                ->subject('Request Approved')
                ->line('Your request has been approved.')
                ->line("Request Number: {$this->requestData['request_number']}")
                ->line("Items Approved:")
                ->line($this->formatItems($this->requestData['items']))
                ->action('View Request', url('/app/stock-outs'), 'danger')
                ->line('Thank you for using our system.');
        }
    }

 
    protected function formatItems($items)
    {
        $formattedItems = '';
        foreach ($items as $item) {
            $formattedItems .= "- {$item['name']} (Quantity: {$item['quantity']})";
        }
        return $formattedItems;
    }

   
    public function toArray($notifiable)
    {
        return [
            'request_number' => $this->requestData['request_number'],
            'requested_by' => $this->requestData['requested_by'],
            'wo_id' => $this->requestData['wo_id'],
            'items' => $this->requestData['items'],
            'type' => $this->type,
        ];
    }
}