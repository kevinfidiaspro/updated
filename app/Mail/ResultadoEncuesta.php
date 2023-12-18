<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResultadoEncuesta extends Mailable
{
    use Queueable, SerializesModels;

    public $resultado;

    public function __construct($resultado){
      $this->resultado = $resultado;
    }

    public function build(){
        $view = ($this->resultado == 0) ? 'emails.encuesta.fatal' : ($this->resultado == 1 ? 'emails.encuesta.regular' : 'emails.encuesta.bien');

        return $this->view($view)
          ->subject('Resultados encuesta');
    }
}
