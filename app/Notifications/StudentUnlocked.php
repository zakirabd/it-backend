<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class StudentUnlocked extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $user;
    public $reply_mail;

    public function __construct(User $user)
    {
        $this->user = $user;
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
            ->greeting("Dear {$this->user->full_name}")
            ->replyTo($this->reply_mail)
            ->subject(Lang::get('CELT Account Unlocked'))
            ->line('We have good news for you. Your account is unlocked and active now.You do not need to take any action.')
            ->line('')
            ->line('Thanks for understanding us and helping us to make more quality products for you.')
            ->line(Lang::get('If you have any questions, please send an email to celtenglish@celt.az'))
            ->line('Thanks for being part of CELT Colleges');
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
