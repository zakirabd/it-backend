<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class StudentPaymentNotify extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $user;
    private $remain_class;

    public function __construct(User $user,$remain_class)
    {
        $this->user = $user;
        $this->remain_class = $remain_class;
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
        ->subject(Lang::get('CELT Payment Reminder'))
        ->line('You have done a great job so far. Please keep going')
        ->line('Now, you have ' . $this->remain_class . ' classes remaining. Please make a payment in your center before your 12th class.')
        ->line('After 12 classes finished, CELT Headquarter Office will lock your account. Your account will be unlocked again once you complete your payment.
        (if you have already made your pay , the office will unlock it for you).')
        ->line('If you have made the payment before the email, please ignore the email, the office will take care of it.')
        ->line('Thanks for understanding us and helping us to make more quality products for you. ')
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
