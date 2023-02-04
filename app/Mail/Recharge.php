<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Recharge extends Mailable
{
    use Queueable, SerializesModels;
     public $amountadd;
     public $solde;
     public $cartnumer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($amountadd, $solde,$cartnumer)
    {
        $this->amountadd = $amountadd;
        $this->solde = $solde;
        $this->cartnumer =$cartnumer;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Recharge',
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
            view: 'emails.recharge',
            with:[
               'amountadd'=>$this->amountadd,
               'solde'=>$this->solde,
               'cartnumer'=>$this->cartnumer,
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