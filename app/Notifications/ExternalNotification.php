<?php
/**
 * Notification créée par l'extérieure.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Interfaces\Model\CanBeNotifiable;
use App\Interfaces\Model\CanNotify;
use App\Models\Model;
use Illuminate\Notifications\Messages\MailMessage;

class ExternalNotification extends Notification
{
    use Queueable;

    protected $subject;
    protected $content;
    protected $action;

    /**
     * Définition du type de notif et sa description.
     *
     * @param CanNotify $model
     * @param string    $content
     * @param array     $action
     */
    public function __construct(CanNotify $model, string $content, array $action=[])
    {
        parent::__construct('external_'.\ModelResolver::getNameFromObject($model));

        $this->subject = $model->name;
        $this->content = $content;
        $this->action = $action;
    }

    /**
     * Renvoie l'action.
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        return $this->action;
    }

    /**
     * Renvoie le sujet de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return $this->subject;
    }

    /**
     * Renvoie le contenu texte de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return $this->content;
    }
}
