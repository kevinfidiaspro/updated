<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FacturasMensualesMail extends Mailable
{
    use Queueable, SerializesModels;
    public $exitosos;
    public $fallidos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($exitosos,$fallidos)
    {
        $this->exitosos = $exitosos;
        $this->fallidos = $fallidos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.facturas_mensuales',  ['exitosos' => $this->exitosos,'fallidos'=>$this->fallidos]);
    }
}
