<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoverPassword extends Mailable
{
  use Queueable, SerializesModels;

  public $password;

  public function __construct($password){
    $this->password = $password;
  }

  public function build(){
    return $this->subject('Acceso Restablecido FidiasPro')
      ->markdown('emails.user.recover_password', ['password' => $this->password]);
  }
}
