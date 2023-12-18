<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fichar extends Model
{
    use HasFactory;

    protected $table = 'fichajes';

    protected $fillable = [
      'usuario_id',
      'fecha',
    ];  

    public function usuario(){
      return $this->belongsTo(User::class, 'usuario_id');
    }

}
