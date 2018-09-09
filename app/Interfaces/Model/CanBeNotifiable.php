<?php

namespace App\Interfaces\Model;

Interface CanBeNotifiable {
    /**
     * Donne les canaux de notification
     * @return array
     */
    public function notificationChannels(string $notificationType): array;
}
