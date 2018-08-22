<?php

namespace App\Notifications\User;

use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class UserDeletion extends Notification
{
    public function __construct() {
        parent::__construct('user');
    }

    protected function getSubject(CanBeNotifiable $notifiable) {
        return 'Suppression de votre compte utilisateur';
    }

    protected function getContent(CanBeNotifiable $notifiable) {
        return 'Nous espérons vous revoir';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        $channels = parent::via($notifiable);

        if (($key = array_search('database', $channels)) !== false)
    		unset($channels[$key]);

        return $channels;
    }

    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail) {
        return $mail
            ->success()
            ->line($notifiable->name)
            ->line('Votre compte a été supprimé avec succès !')
            ->line('Nous espérons vous revoir');
    }
}
