<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
class PostActualizadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $aceptado;

    public function __construct(User $user, $aceptado){
        $this->user = $user;
        $this->aceptado = $aceptado;
    }

    public function build(){
        return $this->markdown('emails.user.post_actualizado', ['user' => $this->user, 'aceptado' => $this->aceptado])->subject('Post '.($this->aceptado?'Aceptado':'Rechazado'));
    }
}
