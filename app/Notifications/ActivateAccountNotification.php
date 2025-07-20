<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use App\Enums\Roles;

/**
 * Class ActivateAccountNotification
 *
 * @package App\Notifications
 */
class ActivateAccountNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var string  */
    public static $redirectToMobileApp = "https://the-marshall-group-web-app.vercel.app";

    /** @var string  */
    private const CLIENT = "/sign-up/client/congratulations/";

    /** @var string  */
    private const CANDIDATE = "/sign-up/profile-review";

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Get the notification's channels.
     *
     * @param  mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) return call_user_func(static::$toMailCallback, $notifiable);

        return $this->getMailMessage(
            $notifiable,
            $notifiable->role_id === Roles::CLIENT ?
                static::$redirectToMobileApp.self::CLIENT.$notifiable->id :
                static::$redirectToMobileApp.self::CANDIDATE
        )->view('email.custom_email', [
            'user' => $notifiable,
            'verificationUrl' => $this->verificationUrl($notifiable),
        ]);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey()]
        );
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }

    /**
     * @param $notifiable
     * @param $url
     *
     * @return MailMessage
     */
    private function getMailMessage($notifiable, $url): MailMessage
    {
        return (new MailMessage)
            ->subject('Marshall Group Verification')
            ->greeting('Marshall Group Verification')
            ->line('Dear '. $notifiable->first_name . ' ' . $notifiable->last_name . ' !' )
            ->line('Thank you for signing up with Marshall Group!
            To ensure the security of your account and to keep you informed about our latest updates, we need to verify your email address.')
            ->line('To verify your email please click on the link bellow:')

            ->action(
                'Verify',
                $this->verificationUrl($notifiable)
            )
            ->line('Once you verify your email, please login to the app and complete creating your profile.')
            ->line("If you didn't sign up for Marshall Group account, or if you believe this email was sent in error, please disregard this message.");
    }
}
