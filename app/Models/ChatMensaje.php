<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMensaje extends Model
{
    use HasFactory;

    protected $table = 'chat_mensaje';

    protected $fillable = ['chat_id', 'mensaje', 'from_user', 'to_user', 'visto'];

    public function chat(){
      return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function sender(){
      return $this->belongsTo(User::class, 'from_user');
    }

    public function receiver(){
      return $this->belongsTo(User::class, 'to_user');
    }
}
