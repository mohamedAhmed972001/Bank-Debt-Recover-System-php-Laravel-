<?php

namespace App\Notifications;

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class UpdatingInvoiceStatus extends Notification
{
    use Queueable;
    public $invoice_status,$invoice_id;

    /**
     * Create a new notification instance.
     */
    public function __construct($invoice_id,$invoice_status)
    {
        $this->invoice_status=$invoice_status;
        $this->invoice_id=$invoice_id;



    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url='http://127.0.0.1:8000/api/invoices_show/'.$this->invoice_id;
        return (new MailMessage)
                    ->greeting('Hello '.Auth::user()->name)
                    ->subject('Invoice Updating')
                    ->line('The ststus of your Invoice is updating.')
                    ->line('Now It is .'.$this->invoice_status)
                    ->action(' Show Invoice', url($url))
                    ->line('Thank you for using our application to manage your Invoices!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
