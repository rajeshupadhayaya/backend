<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class EmailVerify extends Notification
{
    use Queueable;
    protected $query;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($query)
    {
        //
        $this->query = $query;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->query->verifyUser->token;

        $url = env('APP_URL_UI') . '/verify-email/' . (string)$this->query->id . '/' . $verificationUrl;
        Log::debug($url);
        return (new MailMessage)
            ->subject('Email Verification')
            ->greeting('Hello ' . $this->query->name . '!')
            ->line('Please find below your email verification link.')
            ->action(Lang::getFromJson('Verify Email Address'), $url)
            ->line('Thank you for using ' . config('app.name') . '!');
    }



    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
