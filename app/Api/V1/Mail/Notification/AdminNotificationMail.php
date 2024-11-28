<?php 

namespace App\Api\V1\Mail\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function build()
    {
        return $this->view('mails.admin_notification')
                    ->with([
                        'title' => $this->title,
                        'body' => $this->body,
                    ]);
    }
}
