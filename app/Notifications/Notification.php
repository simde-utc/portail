<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

abstract class Notification extends BaseNotification
{
    use Queueable;

    protected $type;
    protected $description;

    /**
     * Définition du type de notif et sa description
     * @param string $type        [description]
     * @param string $description [description]
     */
    public function __construct(string $type, string $description) {
        $this->type = $type;
        $this->description = $description;
    }

    public function getType() {
        return $this->type;
    }

    public function getDescription() {
        return $this->description;
    }

    protected function getSubject(CanBeNotifiable $notifiable) {
        return $this->description;
    }

    abstract protected function getContent(CanBeNotifiable $notifiable);
    abstract protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail);

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(CanBeNotifiable $notifiable)
    {
        return $notifiable->notificationChannels($this->type);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(CanBeNotifiable $notifiable)
    {
        return $this->getMailBody(
            $notifiable,
            (new MailMessage)->line('Bonjour,')
        )
            ->line('')
            ->line('Il y a une vie après les cours,')
            ->line('L\'équipe du SiMDE');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'content' => $this->getContent($notifiable)
        ];
    }
}
