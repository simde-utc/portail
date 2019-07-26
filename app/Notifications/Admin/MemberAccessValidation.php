<?php
/**
 * Notify the user of his access validation.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications\Admin;

use App\Admin\Models\Admin;
use App\Models\AssoAccess;
use App\Notifications\Notification;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class MemberAccessValidation extends Notification
{
    protected $access;
    protected $admin;

    /**
     * Notification's type declaration.
     *
     * @param AssoAccess $access
     * @param Admin      $admin
     */
    public function __construct(AssoAccess $access, Admin $admin)
    {
        $this->access = $access;
        $this->admin = $admin;

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
        return ($this->access->validated ? 'Validation' : 'Refus').' d\'une demande d\'accès';
    }

    /**
     * Notification's text content.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        $status = ($this->access->validated ? 'acceptée' : 'refusée');

        return <<<CONTENT
La demande d'accès {$this->access->access->name} pour l'association {$this->access->asso->shortname} a été $status.
Opération réalisée par {$this->admin->name}.

Commentaire:
{$this->access->comment}


Pour toute demande, se référer à son président d'association et à son pôle.
CONTENT;
    }
}
