<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\Roles;
use App\Models\User;

/**
 * Class RegisterNewAccountNotification
 *
 * @package App\Notifications
 */
class RegisterNewAccountNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * @var User $user
     */
    protected $user;

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
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param $notifiable
     * @param $url
     *
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Marshall Group - New Member')
            ->greeting('Marshall Group - New Member')
            ->line('Dear '. $notifiable->first_name . ' ' . $notifiable->last_name . ' ,' )
            ->line('We are excited to inform you that a new '. strtolower(Roles::getKey($this->user->role_id)) .' has just signed up for The Marshall Group!
            Here are the initial details of our newest member:')
            ->line($this->user->first_name)
            ->line( $this->user->last_name)
            ->line('Sign up date: ' . date_format(now(),"d/m/Y"))
//            ->action(
//                'Verify',
//                'fefdfdfd'
//            )
            ->line('Best Regards,')
            ->line("Marshall Group")
            ->view('email.custom_email_for_admin', [
                'admin' => $notifiable,
                'user' => $this->user
            ]);
    }
}
