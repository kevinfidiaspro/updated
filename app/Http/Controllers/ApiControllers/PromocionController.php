<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromocionRequest;
use Illuminate\Http\Request;
use App\Models\Promocion;
use Storage;
use File;
use DB;

class PromocionController extends Controller
{
  public function getAllpromociones(){
    $promociones = Promocion::orderBy('created_at', 'DESC')->get();
    
    return response()->json($promociones, 200);
  } 

  public function getPromocionById($promocion_id){
    $promocion = (Promocion::find($promocion_id));
    return response()->json(['promocion' => $promocion],200);
  }

  public function deletePromocion($promocion_id){
    $promocion = Promocion::find($promocion_id);
    if(Storage::disk('promociones')->exists($promocion->file_name)){
       Storage::disk('promociones')->delete($promocion->file_name);
    }
    $promocion->delete();
    return response()->json('Promocion eliminada', 200);
  }

  public function changePromocionActive($promocion_id){
    $promocion = Promocion::find($promocion_id);
    $promocion->active = !$promocion->active;
    if($promocion ->active){
      $this->NotificarPromocion($promocion->nombre);
    }
    $promocion->save();
    return response()->json('Estado actualizado', 200);
  }

  public function savePromocion(Request $request){
    $old_file = null;
    $promocion = Promocion::find($request->id);
    if($promocion){ $old_file = $promocion->file_name; }
    $has_promocion = (File::isFile($request->file_name)) ? true : false;

    try {
      $promocion = Promocion::updateOrCreate(['id' => $request->id], $request->except(['promocion_name', 'path']));
      $promocion->url = $promocion->path;
      $promocion->save();

      if($old_file && $has_promocion)
      {
        if(Storage::disk('promociones')->exists($promocion->file_name) && Storage::disk('promociones')->exists($old_file))
        {
          Storage::disk('promociones')->delete($old_file);
        }
      }
      $this->NotificarPromocion($promocion->nombre);
      return response()->json($promocion, 200);
    }
    catch (Exception $exception) {
      return response()->json('error', 301);
    }
  }
  public function NotificarPromocion($name){
    app('App\Http\Controllers\ApiControllers\FCMNotificationController')->SendNotificationToAll("promociones","Promocion","Aprovecha la Promocion: ".$name,"promociones");
  }
}
