<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Interfaces\Model\CanBeNotifiable;
use App\Models\Model;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExternalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $type;
    protected $subject;
    protected $content;
    protected $action;

    /**
     * Définition du type de notif et sa description
     * @param string $type
     */
    public function __construct(Can $model, string $content, array $action = []) {
        parent::__construct('external_'.\ModelResolver::getName($model));

        $this->subject = $model->name;
        $this->content = $content;
        $this->action = $action;
    }

    public function getType() {
        return $this->type;
    }

    protected function getAction(CanBeNotifiable $notifiable) {
        return $this->action;
    }

    protected function getSubject(CanBeNotifiable $notifiable) {
        return $this->subject;
    }

    protected function getContent(CanBeNotifiable $notifiable) {
        return $this->content;
    }

    // On ne permet pas l'envoie de mail
    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail) {
        return null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(CanBeNotifiable $notifiable) {
        $channels = parent::via($notifiable);

        if (($key = array_search('mail', $channels)) !== false)
    		unset($channels[$key]);

        return $channels;
    }
}
