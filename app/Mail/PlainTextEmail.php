<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class PlainTextEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $emailData;


    public function __construct($emailData)
    {
        //

        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     */


    public function envelope(): Envelope
    {
        $envelope = new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: $this->emailData['subject'],
        );

        // Add reply-to if configured
        if (config('mail.reply_to.address')) {
            $envelope->replyTo = [
                new Address(
                    config('mail.reply_to.address'),
                    config('mail.reply_to.name') ?? config('mail.from.name')
                )
            ];
        }

        return $envelope;
    }


    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(

            view: 'emails.plain_text_email',
            with: [
                'emailData' => $this->emailData,
            ],


        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Add attachment if exists
        if (
            !empty($this->emailData['attachment_full_path']) &&
            file_exists($this->emailData['attachment_full_path'])
        ) {
            $attachments[] = Attachment::fromPath($this->emailData['attachment_full_path']);
        }

        return $attachments;
    }




}
