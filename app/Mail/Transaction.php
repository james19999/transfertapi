<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Transactions extends Mailable
{
    use Queueable, SerializesModels;
     public $amount;
     public $title;
     public $companyName;
     public $restant;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($amount,$title,$companyName,$restant)
    {
        $this->amount = $amount;
        $this->title = $title;
        $this->companyName = $companyName;
        $this->restant = $restant;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Transaction',
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
            view: 'emails.transaction',
            with: [
                'amount' => $this->amount->amount,
                'title' => $this->title->title,
                'companyName' => $this->companyName->companyName,
                'restant' => $this->restant->restant,
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