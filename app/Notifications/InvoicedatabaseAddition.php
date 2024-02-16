<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class InvoicedatabaseAddition extends Notification
{
    use Queueable;
    public $invoice_id,$store_or_update_or_changestatus;


    /**
     * Create a new notification instance.
     */
    public function __construct($invoice_id,$store_or_update_or_changestatus)
    {
        $this->invoice_id=$invoice_id;
        $this->store_or_update_or_changestatus=$store_or_update_or_changestatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        if($this->store_or_update_or_changestatus==1){
            return [

                //'data' => $this->details['body']
                'id'=> $this->invoice_id,
                'title'=>'add invoice',
                'user'=> Auth::user()->name,

            ];
        }
        elseif($this->store_or_update_or_changestatus==2){
            return [

                //'data' => $this->details['body']
                'id'=> $this->invoice_id,
                'title'=>'update invoice',
                'user'=> Auth::user()->name,

            ];
        }
        elseif($this->store_or_update_or_changestatus==3){
            return [

                //'data' => $this->details['body']
                'id'=> $this->invoice_id,
                'title'=>'change status is sussc',
                'user'=> Auth::user()->name,

            ];

        }

    }
}
