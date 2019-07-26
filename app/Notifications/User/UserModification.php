<?php
/**
 * Notification that indicates an account modification.
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

class UserModification extends Notification
{
    protected $modifications;

    /**
     * Declare the notification type and indicates modifications.
     *
     * @param array $modifications
     */
    public function __construct(array $modifications)
    {
        parent::__construct('user');

        $this->modifications = $modifications;
    }

    /**
     * Achievable action through the notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        return [
            'name' => 'Voir les modifications',
            'url' => url('profile'),
        ];
    }

    /**
     * Notification subject.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return 'Modification de votre compte utilisateur';
    }

    /**
     * Notification's text content.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return 'Vos données ont été modifiées: '.implode(', ', array_keys(array_change_key_case($this->modifications)));
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
        $mail = $mail
            ->success()
            ->line($notifiable->name)
            ->line('Votre compte a été modifiée:');

        foreach ($this->modifications as $name => $value) {
            $mail = $mail->line(' - '.$name.': '.$value);
        }

        return $mail;
    }
}
