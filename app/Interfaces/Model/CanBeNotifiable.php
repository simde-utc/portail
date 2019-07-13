<?php
/**
 * Indicates that the model is notifiable.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use App\Notifications\Notification;

interface CanBeNotifiable
{
    /**
     * Returns the notification channels.
     *
     * @param string $notificationType
     * @return array
     */
    public function notificationChannels(string $notificationType): array;

    /**
     * Send a notification.
     *
     * @param  mixed $instance
     * @return void
     */
    public function notify($instance);

    /**
     * Returns the notification icon as the creator.
     *
     * @param  Notification $notification
     * @return void
     */
    public function getNotificationIcon(Notification $notification);
}
