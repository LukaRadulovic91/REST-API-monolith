<?php

namespace App\ExpoPushNotifications;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;

/**
 * Class PushNotificationsAbstract
 *
 * @package App\ExpoPushNotifications
 */
abstract class PushNotificationsAbstract extends Notification implements ConstructMessage
{
    /**
     * @return mixed
     */
    public function getQuery(): mixed
    {}

    /**
     * @return string
     */
    public function getTitle(): string
    {}

    /**
     * @return string
     */
    public function getBody(): string
    {}

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
    protected function toExpoNotification($notifiable = null): ExpoMessage
    {
        return (new ExpoMessage())
            ->to($this->getQuery())
//            ->to([$notifiable->expoTokens->first()->value])
            ->title($this->getTitle())
//            ->title('This notification is sent')
            ->body($this->getBody())
//            ->body('This is a content')
            ->channelId('default');
    }
}
