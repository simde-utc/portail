<?php
/**
 * Channel to send external emails.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications;

use App\Interfaces\Model\CanBeNotifiable;

class ExternalChannel
{
    /**
     * Send the given notification.
     *
     * @param  CanBeNotifiable      $notifiable
     * @param  ExternalNotification $notification
     * @return void
     */
    public function send(CanBeNotifiable $notifiable, ExternalNotification $notification)
    {
        $notification->toExternalMail($notifiable);
    }
}
