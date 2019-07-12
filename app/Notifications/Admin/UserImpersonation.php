<?php
/**
 * Notifies the user when an Impersonation occures.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

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
     * Notification's type declaration.
     *
     * @param Admin   $admin
     * @param string  $description
     * @param boolean $asAdmin
     * @return UserImpersonation
     */
    public function __construct(Admin $admin, string $description, bool $asAdmin)
    {
        $this->admin = $admin;
        $this->description = $description;
        $this->asAdmin = $asAdmin;

        parent::__construct('admin', null, $admin->getUser());
    }

    /**
     * Notification's subject.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return 'Connexion d\'un administrateur sur votre compte';
    }

    /**
     * Notification's text content.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return <<<CONTENT
L'administrateur {$this->admin->name} s'est actuellement connecté sous votre compte pour effectuer des opérations.

Raisons:
{$this->description}


Si l'opération n'est pas désirée, veuillez le signaler.
CONTENT;
    }
}
