<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InformationCart extends Mailable
{
    use Queueable, SerializesModels;
    public $name ;
    public $number ;
    public $amount;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $number, $amount)
    {
        $this->name = $name;
        $this->number = $number;
        $this->amount = $amount;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Information Cart',
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
            view: 'emails.informationcart',
            with:[
             'name' =>   $this->name ,
              'number'=>  $this->number ,
              'amount'=>  $this->amount,
            ],
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