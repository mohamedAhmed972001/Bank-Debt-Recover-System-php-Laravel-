<?php

namespace App\Notifications;

use Ichtrojan\Otp\Models\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;


class EmailVerificationNotification extends Notification
{
    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    public $otp;

    use Queueable;
    use Notifiable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->message='verification process';
        $this->subject='verification Needed';
        $this->fromEmail='mezo1927061@gmail.com';
        $this->mailer='smtp';
        $this->otp=new Otp();
        //
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
       // $otp=$this->otp->generate($notifiable->email,6,60);
        return (new MailMessage)
            ->line('The introduction to the notification.')
           // ->action('Notification Action', $this->verificationUrl($notifiable))
            ->line('Thank you for using our application!');
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
