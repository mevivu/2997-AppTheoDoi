<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;


class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;

    public string $body;

    // public string $driverName;

    // public string $driverPhone;

    public int $notificationId;

    public string $url;


    /**
     * Create a new message instance.
     *
     * @param string $title The title of the email.
     * @param string $body The body text of the email.
     * @throws Exception
     */
    public function __construct(string $title, string $body, $notificationId)
    {
        $this->title = $title;
        $this->body = $body;
        $this->notificationId = $notificationId;
        $this->url = URL::route('admin.notification.edit', ['id' => $this->notificationId]);
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.admin_notification',
        );
    }


}
