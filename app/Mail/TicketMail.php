<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $string;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ticket, $string)
    {
        $this->ticket = $ticket;
        $this->string = $string;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->string == 'create' ? 'Creación de ticket' : 'Asignación de fecha de finalización de ticket';
        return $this->markdown('emails.tickets.tickets')->subject($subject);
    }
}
