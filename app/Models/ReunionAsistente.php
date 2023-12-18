<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReunionAsistente extends Model
{
    use HasFactory;
    protected $table = 'reunion_asistente';
    protected $fillable = [
        'id_reunion',
        'id_asistente'
    ];
    public function Reunion(){
        return $this->hasOne(Reunion::class,'id','id_reunion');
    }
    public function Asistente(){
        return $this->hasOne(User::class,'id','id_asistente');
    }
}
