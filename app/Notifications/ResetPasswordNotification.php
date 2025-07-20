<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class ResetPasswordNotification
 *
 * @package App\Notifications
 */
class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $resetLink;
    public $expirationInMinutes;

    /**
     * Create a new notification instance.
     *
     * @param $resetLink
     * @param $expirationInMinutes
     */
    public function __construct($resetLink, $expirationInMinutes)
    {
        $this->resetLink = $resetLink;
        $this->expiration = now()->addMinutes($expirationInMinutes);
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
        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $this->resetLink)
            ->line('This password reset link will expire on ' . $this->expiration->toDateTimeString())
            ->line('If you did not request a password reset, no further action is required.')
            ->view('email.reset_password_email', [
                'user' => $notifiable,
                'verificationUrl' => $this->resetLink,
            ]);
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
