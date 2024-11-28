<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApproveDriverMail extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct()
    {
        //
    }



    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận tài khoản tài xế',
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
            view: 'mails.approve_driver',
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}