<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table="producto";
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'minutos',
        'id_modulo'
    ];
    public function Modulo(){
        return $this->hasOne(ProductoModulo::class,'id','id_modulo');
    }
}
