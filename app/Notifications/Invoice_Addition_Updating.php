<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Invoice_Addition_Updating extends Notification
{
    use Queueable;
    public $invoice_id,$store_or_update;

    /**
     * Create a new notification instance.
     */
    public function __construct($invoice_id,$store_or_update)
    {
        $this->invoice_id=$invoice_id;
        $this->store_or_update=$store_or_update;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url='http://127.0.0.1:8000/api/invoices_show/'.$this->invoice_id;
        if($this->store_or_update==1){
            return (new MailMessage)
                ->greeting('Hello')
                ->subject('Invoice Addition')
                ->line('Add New Invoice')
                ->action(' Show Invoice', url($url))
                ->line('Thank you for using our application to manage your Invoices!');
        }
        else{
            return (new MailMessage)
                ->greeting('Hello')
                ->subject('Invoice Updating')
                ->line('Update Invoice')
                ->action(' Show Invoice', url($url))
                ->line('Thank you for using our application to manage your Invoices!');
        }

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
//    public function toDatabase($notifiable)
//    {
//        return [
//
//            //'data' => $this->details['body']
//            'id'=> $this->invoice_id,
//            'title'=>'تم اضافة فاتورة جديد بواسطة :',
//            'user'=> Auth::user()->name,
//
//        ];
//    }
}
