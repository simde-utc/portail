<?php

namespace App\Notifications\Admin;

use App\Admin\Models\Admin;
use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class UserImpersonation extends Notification
{
    protected $admin;
    protected $description;
    protected $asAdmin;

    /**
     * Déclare le type de notification.
     */
    public function __construct(Admin $admin, string $description, bool $asAdmin)
    {
        $this->admin = $admin;
        $this->description = $description;
        $this->asAdmin = $asAdmin;

        parent::__construct('admin');
    }

    /**
     * Sujet de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return 'Connexion d\'un administrateur sur votre compte';
    }

    /**
     * Contenu texte de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        $adminText = $this->asAdmin ? 'Il s\'est aussi connecté en tant qu\'administrateur avec votre compte.'.PHP_EOL : '';

        return <<<CONTENT
L'administrateur {$this->admin->name} s'est actuellement connecté sous votre compte pour effectuer des opérations.
{$adminText}

Raisons:
{$this->description}


Si l'opération n'est pas désirée, veuillez le signaler.
CONTENT;
    }
}
