<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
use File;

class Promocion extends Model
{
    protected $table = 'promocion';

    protected $fillable = ['nombre', 'file_name', 'url', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = ['promocion_name', 'path'];

    public function getPathAttribute(){
       return asset('storage/promociones/' . $this->file_name);
    }

    public function getPromocionNameAttribute(){
      return substr($this->file_name, strpos($this->file_name, '-') + 1);
    }

    public function setActiveAttribute($active){
      $this->attributes['active'] = ($active == 'true') ? 1 : 0;
    }

    public function setFileNameAttribute($promocion){
        if(!$promocion || !File::isFile($promocion)){
    		return null;
    	}
    	$file_name = uniqid() . '-' . $promocion->getClientOriginalName();
        Storage::disk('promociones')->put($file_name,  File::get($promocion));
    	$this->attributes['file_name'] = $file_name;
    }

}
