<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HorasExcedidasMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($proyecto,$minutos)
    {
        $this->proyecto = $proyecto;
        $this->minutos = $minutos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.horas_excedidas',  ['proyecto' => $this->proyecto,'minutos'=> $this->minutos]);
    }
}
