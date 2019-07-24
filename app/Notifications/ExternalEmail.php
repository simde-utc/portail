<?php
/**
 * Send an external email.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExternalEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $html;

    /**
     * Define the subject and the content of the email.
     *
     * @param string $subject
     * @param string $html
     */
    public function __construct(string $subject, string $html)
    {
        $this->subject = $subject;
        $this->html = $html;
    }

    /**
     * Return the email content.
     *
     * @return string
     */
    public function build(): string
    {
        return $this->html;
    }
}
