<?php

namespace App\ExpoPushNotifications;

use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

/**
 * Class CandidatePushNotifications
 *
 * @package App\ExpoPushNotifications
 */
final class CandidatePushNotifications extends PushNotificationsAbstract
{
    /**
     * @param null $notifiable
     *
     * @return ExpoMessage
     */
    public function toExpoNotification($notifiable = null): ExpoMessage
    {
        return parent::toExpoNotification($notifiable);
    }
}
