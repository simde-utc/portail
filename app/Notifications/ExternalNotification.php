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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class ExternalNotification extends Notification
{
    use Queueable;

    protected $subject;
    protected $content;
    protected $html;
    protected $data;
    protected $action;

    /**
     * Create an external notification.
     *
     * @param CanNotify $model
     * @param string    $subject
     * @param string    $content
     * @param string    $html
     * @param array     $action
     * @param array     $data        Data for placed variables in subject, content, action and html.
     * @param array     $exceptedVia
     * @param Model     $creator
     */
    public function __construct(CanNotify $model, string $subject=null, string $content=null, string $html=null,
        array $action=[], array $data=[], array $exceptedVia=[], Model $creator=null)
    {
        parent::__construct('external_'.\ModelResolver::getNameFromObject($model), null, $creator);

        $this->subject = ($subject ?? $model->getName());
        $this->content = $content;
        $this->html = $html;
        $this->action = $action;
        $this->data = $data;
        $this->exceptedVia = $exceptedVia;
        $this->icon = $creator->image;
    }

    /**
     * Parse all values to define the values.
     *
     * @param  string $string
     * @return string|null
     */
    protected function parseString(string $string=null): ?string
    {
        foreach ($this->data as $key => $value) {
            $string = \str_replace('${'.$key.'}', $value, $string);
        }

        return $string;
    }

    /**
     * Return the sender data.
     *
     * @return array
     */
    public function getSenderData(): array
    {
        return [$this->creator->email, $this->creator->name];
    }

    /**
     * Return the email notification representation.
     *
     * @param  CanBeNotifiable $notifiable
     * @return MailMessage
     */
    public function toMail(CanBeNotifiable $notifiable): MailMessage
    {
        $sender = $this->getSenderData();

        return parent::toMail($notifiable)->from(...$sender);
    }

    /**
     * Return the external email notification representation.
     *
     * @param  CanBeNotifiable $notifiable
     * @return void
     */
    public function toExternalMail(CanBeNotifiable $notifiable): void
    {
        $sender = $this->getSenderData();

        Mail::to($notifiable)->send(
            (new ExternalEmail($this->getSubject($notifiable), $this->getHtml($notifiable)))->from(...$sender)
        );
    }

    /**
     * List all notification channels.
     * Here, in order to send an external email, we need to switch mail to our custom external channel.
     *
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    public function via(CanBeNotifiable $notifiable)
    {
        $channels = parent::via($notifiable);

        if ($this->html) {
            if (($key = array_search('mail', $channels)) !== false) {
                $channels[$key] = ExternalChannel::class;
            }
        }

        return $channels;
    }

    /**
     * Action réalisable via la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        return array_map(function ($value) {
            $this->parseString($value);
        }, $this->action);
    }

    /**
     * Sujet de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getSubject(CanBeNotifiable $notifiable)
    {
        return $this->parseString($this->subject);
    }

    /**
     * Contenu texte de la notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getContent(CanBeNotifiable $notifiable)
    {
        return $this->parseString($this->content);
    }

    /**
     * Html content for an email.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    protected function getHtml(CanBeNotifiable $notifiable)
    {
        return $this->parseString($this->html);
    }
}
