<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\RegisterNewAccountNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * Class SendAdminEmailNotification
 *
 * @package App\Listeners
 */
class SendAdminEmailNotification
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $admin = User::where('email', '=', 'marianne@themarshallgroup.ca')->first();
        $admin->notify(new RegisterNewAccountNotification($event->user));
    }
}
