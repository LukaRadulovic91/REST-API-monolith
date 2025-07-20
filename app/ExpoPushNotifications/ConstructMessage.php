<?php

namespace App\ExpoPushNotifications;

/**
 * Interface ConstructMessage
 *
 * @package App\ExpoPushNotifications
 */
interface ConstructMessage
{
    /**
     * @return mixed
     */
    public function getQuery(): mixed

    /**
     * @return string
     */
    public function getTitle(): string

    /**
     * @return string
     */
    public function getBody(): string
}
