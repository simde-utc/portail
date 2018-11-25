<?php
/**
 * Notification de base.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

abstract class Notification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected $type;

    /**
     * Définition du type de notification.
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Retourne le type de notification.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Action réalisable via la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        return [];
    }

    /**
     * Sujet de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    abstract protected function getSubject(CanBeNotifiable $notifiable);

    /**
     * Contenu texte de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    abstract protected function getContent(CanBeNotifiable $notifiable);

    /**
     * Contenu email de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @param  MailMessage     $mail
     * @return MailMessage
     */
    abstract protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail);

    /**
     * Liste les canaux de notifications.
     *
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    public function via(CanBeNotifiable $notifiable)
    {
        return $notifiable->notificationChannels($this->type);
    }

    /**
     * Retourne la réprésentation email de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return MailMessage
     */
    public function toMail(CanBeNotifiable $notifiable)
    {
        $action = $this->getAction($notifiable);
        $mail = $this->getMailBody(
            $notifiable,
            (new MailMessage)->subject($this->getSubject($notifiable))
        );

        if ($action && isset($action['name']) && isset($action['url'])) {
            $mail->action($action['name'], $action['url']);
        }

        return $mail;
    }

    /**
     * Renvoie la notification sous forme de tableau.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
        ];
    }
}
