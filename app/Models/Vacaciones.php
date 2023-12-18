<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacaciones extends Model
{
    use HasFactory;
    protected $table = "vacaciones";
    protected $fillable = ['id_usuario','fecha'];
    public function Usuario(){
        return $this->hasOne(User::class,'id','id_usuario');
    }
}
