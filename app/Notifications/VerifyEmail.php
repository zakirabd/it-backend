<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        return (new MailMessage)
            ->greeting("Dear {$this->user->full_name}")
            ->subject(Lang::get('CELT VIP Email Verification'))
            ->line('Welcome to CELT Colleges. We are happy to have you here.')
            ->line('Please VERIFY your email address. You need to have an active email to use the CELT Colleges System.')
            ->line('If you’re having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:')
            ->action(Lang::get('Verify Email Address'), $verificationUrl)
            ->line(Lang::get('If you have any questions, please send an email to celtenglish@celt.az'))
            ->line('Thanks for joining us');
    }


    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return config('app.url') . '/email/verify/' . $notifiable->getKey() . '/' . sha1($notifiable->getEmailForVerification()) . '?' . http_build_query(
                [
                    'verifyLink' => URL::temporarySignedRoute('verification.verify',Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                        [
                            'id' => $notifiable->getKey(),
                            'hash' => sha1($notifiable->getEmailForVerification()),
                        ]
                    )
                ]
            );
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
