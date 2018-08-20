<?php

namespace App\Notifications\User;

use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class UserDesactivation extends Notification
{
    public function __construct() {
        parent::__construct('user');
    }

    protected function getSubject(CanBeNotifiable $notifiable) {
        return 'Désactivation de votre compte utilisateur';
    }

    protected function getContent(CanBeNotifiable $notifiable) {
        return 'Désactivation du compte';
    }

    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail) {
        return $mail
            ->error()
            ->line($notifiable->name)
            ->line('Votre compte a été désactivé !');
    }
}
