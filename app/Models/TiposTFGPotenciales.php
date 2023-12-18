<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposTFGPotenciales extends Model
{
    use HasFactory;
    protected $table = 'tipos_tfg_potenciales';
    protected $fillable = ['nombre'];
    public function Potencial(){
        return $this->hasMany(TFGPotenciales::class,'id_tipo','id');
    }
}
