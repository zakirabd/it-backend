<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParentPaymentNotify extends Notification implements ShouldQueue
{
    use Queueable;
    public $reply_mail;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $company_id = auth()->user()->company_id;
        $company_email = User::whereIn('role',['company_head','office_manager'])->where('company_id',$company_id)->pluck('email');
        $this->reply_mail=$company_email;

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
        return (new MailMessage)
            ->subject('Payment reminder')
            ->replyTo($this->reply_mail)
            ->greeting("Hello!")
            ->line('Please make a payment. After 12 classes, your childs profile will be locked by Headquarter.')
            ->action('View classes', env('VUE_APP_URL') . '/student/attendance')
            ->line('Thank you for using our application!');
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
