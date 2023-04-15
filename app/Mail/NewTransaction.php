<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTransaction extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $codetansaction;
    public $name;
    public $codecarte;
    public $title;
    public function __construct($codetansaction,$name,$codecarte,$title)
    {
     $this->codetansaction=$codetansaction;
     $this->name=$name;
     $this->codecarte=$codecarte;
     $this->title=$title;

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'New Transaction',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.newtransaction',
            with:[
               'codetansaction'=>$this->codetansaction,
               'name'=>$this->name,
               'codecarte'=>$this->codecarte,
               'title'=>$this->title,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
