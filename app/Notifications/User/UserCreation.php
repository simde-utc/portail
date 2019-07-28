<?php
/**
 * Account creation notification.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications\User;

use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreation extends Notification
{
    /**
     * Notification's type declaration.
     */
    public function __construct()
    {
        parent::__construct('user');
    }

    /**
     * Achievable action trough the notification.
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        return [
            'name' => 'Consulter mon profil',
            'url' => url('profile'),
        ];
    }

    /**
     * Notification's subject.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return 'Création de votre compte utilisateur';
    }

    /**
     * Notification's text content.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return 'Bienvenue sur le Portail des Assos !';
    }

    /**
     * Notification's email content.
     *
     * @param  CanBeNotifiable $notifiable
     * @param  MailMessage     $mail
     * @return MailMessage
     */
    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail)
    {
        return $mail
            ->success()
            ->line($notifiable->name)
            ->line('Votre compte a été créé avec succès !');
    }
}
