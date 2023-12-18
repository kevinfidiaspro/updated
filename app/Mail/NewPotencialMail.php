<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPotencialMail extends Mailable  implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $cliente;

    public $form;

    public function __construct($cliente, $form){
        $this->cliente = $cliente;
        $this->form = $form;
    }

    public function build(){
        return $this->markdown('emails.facebook.new_potencial', ['cliente' => $this->cliente, 'passworformd' => $this->form])->subject('Nuevo Cliente Potencial');
    }
}
