<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chat';

    protected $fillable = ['user_id'];

    protected $casts = [
      'created_at' => 'date:d-m-Y'
    ];

    protected $appends = ['unread'];

    public function getUnreadAttribute(){
      return $this->hasMany(ChatMensaje::class)
        ->where('visto', 0)
        ->where('from_user', '!=',  auth()->user()->id)
        ->count();
	  }

    public function user(){
      return $this->belongsTo(User::class, 'user_id');
    }

    public function mensajes(){
      return $this->hasMany(ChatMensaje::class)->orderBy('created_at', 'ASC');
    }
}
