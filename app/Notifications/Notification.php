<?php

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
     * Définition du type de notif et sa description
     * @param string $type
     */
    public function __construct(string $type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    protected function getAction(CanBeNotifiable $notifiable) {
        return [];
    }

    abstract protected function getSubject(CanBeNotifiable $notifiable);
    abstract protected function getContent(CanBeNotifiable $notifiable);
    abstract protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail);

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(CanBeNotifiable $notifiable) {
        return $notifiable->notificationChannels($this->type);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(CanBeNotifiable $notifiable) {
        $action = $this->getAction($notifiable);
        $mail = $this->getMailBody(
            $notifiable,
            (new MailMessage)->subject($this->getSubject($notifiable))
        );

        if ($action && isset($action['name']) && isset($action['url']))
            $mail->action($action['name'], $action['url']);

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            'type' => $this->type,
            'content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
        ];
    }
}
