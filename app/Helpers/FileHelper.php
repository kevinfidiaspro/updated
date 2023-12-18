<?php

namespace App\Helpers;

use App\Models\Archivo;
use App\Models\Proyecto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public $model;

    public function __construct(Model $model){
      $this->model = $model;
    }

    private function guardarArchivo($archivo, $disk){
      $file_name =  uniqid() . '_' . $archivo->getClientOriginalName();

      Storage::disk($disk)->put($file_name, File::get($archivo));

      $this->model->archivos()->create([
        'url'       => $file_name,
        'file_name' => $archivo->getClientOriginalName()
       ]);

      return response()->json(['success' => 'Imagen ha sido cargada'], 200);
    }
    public function guardarArchivo64($archivo, $disk_name){
      $str = explode(',',$archivo);
      $endtag = explode('/',explode(';',$str[0])[0])[1];
      $image = $str[1];
          //$image = str_replace(' ', '+', $image);
  
       
      $nombre_archivo =  uniqid() . '.'.$endtag;
  
      Storage::disk($disk_name)->put($nombre_archivo, base64_decode($image));
  
      $this->model->archivos()->create(['file_name' => $nombre_archivo,'url'=>$nombre_archivo]);
  
      return response()->json(['success' => 'Imagen ha sido cargada'], 200);
  }
  public function guardarArchivos64($archivos, $disk){
    if (!$archivos){
      return null;
    }
    foreach($archivos as $archivo){
      $this->guardarArchivo64($archivo, $disk);
    }
  }
    public function guardarArchivos($archivos, $disk){
      if (!$archivos){
        return null;
      }
      foreach($archivos as $archivo){
        $this->guardarArchivo($archivo, $disk);
      }
    }

    public function deleteFile($id, $disk){
      $archivo = Archivo::findOrFail($id);
      if(Storage::disk($disk)->exists($archivo->url)){
  		  Storage::disk($disk)->delete($archivo->url);
  		}
      return $archivo->delete();
    }
}
