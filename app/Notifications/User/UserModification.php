<?php

namespace App\Notifications\User;

use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class UserModification extends Notification
{
    protected $modifications = [];

    public function __construct($modifications) {
        parent::__construct('user');

        $this->modifications = $modifications;
    }

    protected function getAction(CanBeNotifiable $notifiable) {
        return [
            'name' => 'Voir les modifications',
            'url' => url('profile'),
        ];
    }

    protected function getSubject(CanBeNotifiable $notifiable) {
        return 'Modification de votre compte utilisateur';
    }

    protected function getContent(CanBeNotifiable $notifiable) {
        return 'Vos données ont été modifiées: '.implode(', ', array_keys(array_change_key_case($this->modifications)));
    }

    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail) {
        $mail = $mail
            ->success()
            ->line($notifiable->name)
            ->line('Votre compte a été modifiée:');

        foreach ($this->modifications as $name => $value)
            $mail = $mail->line(' - '.$name.': '.$value);

        return $mail;
    }
}
