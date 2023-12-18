<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;
use File;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
      'codigo',
      'importe',
      'descripcion',
      'tipo_gasto_id',
      'created_at',
      'archivo',
      'fecha',
      'user_id',
      'proyecto_id'
    ];

    protected $dates = ['created_at'];

    // public function setArchivoAttribute($imagen){
    //   if(!$imagen || !File::isFile($imagen)){
    //     return null;
    //   }
    //   $file_name = uniqid() . '-' . $imagen->getClientOriginalName();
    //   Storage::disk('gastos')->put($file_name,  File::get($imagen));
    //   $this->attributes['archivo'] = $file_name;
    // }

    public function tiposgasto() {
        return $this->belongsTo(TiposGasto::class, 'tipo_gasto_id');
    }

    public function proyecto() {
        return $this->belongsTo(Proyecto::class);
    }
}
