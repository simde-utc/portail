<?php
/**
 * Base notification.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Interfaces\Model\CanBeNotifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

abstract class Notification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected $type;
    protected $creator;
    protected $exceptedVia = [];
    protected $icon;

    /**
     * Notification's type declaration.
     *
     * @param string $type
     * @param string $icon
     * @param Model  $creator
     */
    public function __construct(string $type, string $icon=null, Model $creator=null)
    {
        $this->type = $type;
        $this->icon = $icon;
        $this->creator = ($creator ?? \Auth::user());
    }

    /**
     * Achievable action trough the notification.
     *
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    protected function getAction(CanBeNotifiable $notifiable)
    {
        return [];
    }

    /**
     * Notification subject.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    abstract protected function getSubject(CanBeNotifiable $notifiable);

    /**
     * Notification's text content.
     *
     * @param  CanBeNotifiable $notifiable
     * @return string
     */
    abstract protected function getContent(CanBeNotifiable $notifiable);

    /**
     * Notification email content.
     *
     * @param  CanBeNotifiable $notifiable
     * @param  MailMessage     $mail
     * @return MailMessage
     */
    protected function getMailBody(CanBeNotifiable $notifiable, MailMessage $mail)
    {
        $content = '<br />'.str_replace(PHP_EOL, '<br />', htmlentities($this->getContent($notifiable))).'<br />';

        return $mail
            ->line($notifiable->name)
            ->line(new HtmlString($content));
    }

    /**
     * Lists all notifications channels.
     *
     * @param  CanBeNotifiable $notifiable
     * @return array
     */
    public function via(CanBeNotifiable $notifiable)
    {
        $channels = $notifiable->notificationChannels($this->type);

        foreach ($this->exceptedVia as $excepted) {
            if (($key = array_search($excepted, $channels)) !== false) {
                unset($channels[$key]);
            }
        }

        return $channels;
    }

    /**
     * Returns the notification email representation.
     *
     * @param  CanBeNotifiable $notifiable
     * @return MailMessage
     */
    public function toMail(CanBeNotifiable $notifiable)
    {
        $action = $this->getAction($notifiable);
        $mail = $this->getMailBody(
            $notifiable,
            (new MailMessage)->subject($this->getSubject($notifiable))
        );

        if ($action && isset($action['name']) && isset($action['url'])) {
            $mail->action($action['name'], $action['url']);
        }

        return $mail;
    }

    /**
     * Creator retrievement.
     *
     * @param  Model $notifiable
     * @return Model
     */
    public function getCreator(Model $notifiable): Model
    {
        return ($this->creator ?? $notifiable);
    }

    /**
     * Icon retrievement.
     *
     * @param  mixed $notifiable
     * @return mixed
     */
    public function getIcon($notifiable)
    {
        return ($this->icon ?? $notifiable->getNotificationIcon($this));
    }

    /**
     * Returns the notification under thr form of an array.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'content' => $this->getContent($notifiable),
            'action' => $this->getAction($notifiable),
            'icon' => $this->getIcon($notifiable),
        ];
    }

    /**
     * Returns the notification under the form of an array for the database.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $createdBy = $this->getCreator($notifiable);

        return array_merge($this->toArray($notifiable), [
            'created_by' => [
                'id' => $createdBy->getKey(),
                'type' => get_class($createdBy),
            ],
        ]);
    }
}
