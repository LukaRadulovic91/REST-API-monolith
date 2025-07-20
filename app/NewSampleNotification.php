<?php

namespace App;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;

/**
 * Class NewSampleNotification
 *
 * @package App
 */
class NewSampleNotification extends Notification
{
    /**
     * @var string
     */
    private string $messageTitle;

    /**
     * @var string
     */
    private string $messageBody;

    /**
     * NewSampleNotification constructor.
     *
     * @param string $messageTitle
     * @param string $messageBody
     */
    public function __construct(
        string $messageTitle,
        string $messageBody
    )
    {
        $this->messageTitle = $messageTitle;
        $this->messageBody = $messageBody;
    }
    /**
     * @param $notifiable
     *
     * @return array|string[]
     */
    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    /**
     * @param null $notifiable
     *
     * @return ExpoMessage
     *
     */
    public function toExpoNotification($notifiable = null): ExpoMessage
    {
        return (new ExpoMessage())
            ->to([$notifiable->expoTokens->first()->value])
            ->title($this->messageTitle)
            ->body($this->messageBody)
            ->channelId('default');
    }
}
