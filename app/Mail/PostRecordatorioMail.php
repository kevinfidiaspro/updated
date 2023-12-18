<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
class PostRecordatorioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function build(){
        return $this->markdown('emails.user.post_recordatorio', ['user' => $this->user])->subject('Acción requerida: Post(s) pendiente(s) de revisión');
    }
}
