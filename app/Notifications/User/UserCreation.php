<?php

namespace App\Notifications\User;

use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreation extends Notification
{
    public function __construct() {
        parent::__construct('user');
    }

    protected function getSubject(CanBeNotifiable $notifiable) {
        return 'Création de votre compte utilisateur';
    }

    protected function getContent(CanBeNotifiable $notifiable) {
        return 'Bienvenue sur le Portail des Assos !';
    }

    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail) {
        return $mail
            ->success()
            ->line($notifiable->name)
            ->line('Votre compte a été créé avec succès !');
    }
}
