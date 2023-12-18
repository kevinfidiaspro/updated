<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
use File;

class ContenidoRedSocial extends Model
{
    protected $table = 'contenido_red_social';

    protected $fillable = ['url','id_cliente','file_name','texto','observaciones','id_estado','fecha_publicacion'];

    protected $appends = ['name', 'path'];

    public function Estado(){
      return $this->hasOne(EstadoContenido::class,'id','id_estado');
    }
    public function getPathAttribute(){
       return asset('storage/contenido_red/' . $this->file_name);
    }

    public function getNameAttribute(){
      return substr($this->file_name, strpos($this->file_name, '-') + 1);
    }

    public function setFileNameAttribute($promocion){
        if(!$promocion || !File::isFile($promocion)){
    		return null;
    	}
    	$file_name = uniqid() . '-' . $promocion->getClientOriginalName();
        Storage::disk('contenido_red')->put($file_name,  File::get($promocion));
    	$this->attributes['file_name'] = $file_name;
    }
    public function archivos(){
      return $this->morphMany(Archivo::class, 'archivos');
    }
}
