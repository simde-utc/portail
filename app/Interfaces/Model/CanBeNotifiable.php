<?php
/**
 * Indique que le modèle est notifiable.
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
     * Donne les canaux de notification.
     *
     * @param string $notificationType
     * @return array
     */
    public function notificationChannels(string $notificationType): array;

    /**
     * Envoi une notification.
     *
     * @param  mixed $instance
     * @return void
     */
    public function notify($instance);

    /**
     * Donne l'icône de notification en tant que créateur.
     *
     * @param  Notification $notification
     * @return void
     */
    public function getNotificationIcon(Notification $notification);
}
