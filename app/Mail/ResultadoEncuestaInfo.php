<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResultadoEncuestaInfo extends Mailable
{
  use Queueable, SerializesModels;

  public $resultado;
  public $resultados_admin;
  public $email;

  public function __construct($resultado, $resultados_admin, $email){
    $this->resultado = $resultado;
    $this->resultados_admin = $resultados_admin;
    $this->email = $email;
  }

  public function build(){
      $texto_resultado = ($this->resultado == 0) ? 'Fatal' : ($this->resultado == 1 ? 'Regular' : 'Bien');

      return $this->markdown('emails.encuesta.info', [
        'email'       => $this->email,
        't_resultado' => $texto_resultado,
        'items'       => $this->resultados_admin
      ])->subject('Resultados encuesta');
  }
}
