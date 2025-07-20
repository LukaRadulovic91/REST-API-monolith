<?php

namespace App\ExpoPushNotifications;

use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

/**
 * Class ClientPushNotifications
 *
 * @package App\ExpoPushNotifications
 */
final class ClientPushNotifications extends PushNotificationsAbstract
{
    /**
     * ClientPushNotifications constructor.
     */
    public function __construct()
    {
        $this->getQuery();
        $this->getTitle();
        $this->getBody();
    }

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
