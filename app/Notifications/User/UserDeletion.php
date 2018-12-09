<?php
/**
 * Notification pour signler la suppression du compte.
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

class UserDeletion extends Notification
{
    // On ne souhaite pas garder la notification en bdd.
    protected $exceptedVia = ['database'];

    /**
     * Déclare le type de notification.
     */
    public function __construct()
    {
        parent::__construct('user');
    }

    /**
     * Sujet de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return 'Suppression de votre compte utilisateur';
    }

    /**
     * Contenu texte de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return 'Nous espérons vous revoir';
    }

    /**
     * Contenu email de la notification.
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
            ->line('Votre compte a été supprimé avec succès !')
            ->line('Nous espérons vous revoir');
    }
}
