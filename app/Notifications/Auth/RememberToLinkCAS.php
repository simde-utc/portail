<?php
/**
 * Notification to remember a user to connect his CAS account to a password.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications\Auth;

use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class RememberToLinkCAS extends Notification
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
            'name' => 'Lier mon compte CAS/Portail avec une adresse email',
            'url' => url('login/cas/link'),
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
        return 'Liez votre compte !';
    }

    /**
     * Notification's text content.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return 'Pensez à lier votre compte CAS/Portail avec une adresse email.
            Ceci vous permettra de vous connecter à votre compte lors que votre CAS ne sera plus valide';
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
            ->line('Pensez à lier votre compte CAS/Portail avec une adresse email.')
            ->line('Pour rappel, votre compte a été créé le '.Carbon::parse($notifiable->created_at)->format('d/m/Y à H:i'))
            ->line('En faisant celà, il vous est possible d\'accéder à votre compte même si vous ne faites plus parti
                de l\'UTC et donc garder vos préférences et votre parcours associatif par exemple.');
    }
}
