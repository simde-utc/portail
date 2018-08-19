<?php

namespace App\Notifications\User;

use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreation extends Notification
{
    public function __construct() {
        parent::__construct(
            'user_creation',
            'Création d\'un nouvel utilisateur'
        );
    }

    protected function getContent(CanBeNotifiable $notifiable) {
        return 'Bienvenue sur le Portail des Assos !';
    }

    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail) {
        return $mail
            ->line($notifiable->name)
            ->line('')
            ->line('Votre compte a été créé avec succès !');
    }
}
