<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    protected $table='empresa';
    protected $fillable = [
        'nombre','cif','direccion','codigo_postal','localidad','provincia_id','ccc'
    ];
    public function Provincia(){
        return $this->hasOne(Provincia::class,'id','provincia_id');
    }
}
