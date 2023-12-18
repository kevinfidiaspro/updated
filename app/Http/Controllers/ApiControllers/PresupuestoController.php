<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Resources\PresupuestoResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Recibo;
use App\Models\Nropresupuesto;
use App\Models\Presupuesto;
use App\Mail\PresupuestoMail;
use App\Http\Requests\PresupuestoRequest;
use Illuminate\Support\Facades\Mail;
use App\Models\Proyecto;
use Validator;
use Storage;
use File;

class PresupuestoController extends Controller
{
  public function getRecibos($user_id){
    $presupuestos = PresupuestoResource::collection(Recibo::where('user_id', '=', $user_id)->whereHas('nro_presupuesto')->orderBy('id', 'DESC')->get());
    return response()->json($presupuestos, 200);
  }

  public function deletePresupuesto($recibo_id){
    $recibo = Recibo::find($recibo_id);

    if(Storage::disk('recibos')->exists($recibo->presupuesto_url)){
       Storage::disk('recibos')->delete($recibo->presupuesto_url);
    }

    $recibo->nro_presupuesto()->delete();

    $recibo->presupuesto_url = null;

    $recibo->save();

    return response()->json('nota eliminada', 200);
  }

  public function showPresupuesto($proyecto_id){

    $presupuesto = Proyecto::find($proyecto_id);

    if($presupuesto == null) {
        return response()->json('Proyecto no existe', 400);
    }

    $presupuesto = Presupuesto::with(['proyecto:id,estado_id','items_presupuesto'])->where('id_proyecto', $proyecto_id)->first();
    
    return response()->json($presupuesto, 200);
  }

  public function storePresupuesto($proyecto_id, Request $request){
    $validator = Validator::make($request->all(), [
        'descripcion' => 'required',
        'file'        => 'required_without:file_name',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error'=>$validator->errors()
        ], 422);
    }

    if($request->hasFile('file')){
        $fileName = uniqid() . '_' . $request->file->getClientOriginalName();

        Storage::disk('presupuesto')->put($fileName, File::get($request->file));

        $request['file_name'] = $fileName;
    }

    $proyecto = Proyecto::where('id', $proyecto_id)->get()->first();

    if(!$proyecto) {
        return response()->json('Proyecto no existe', 301);
    }

    $presupuesto = $proyecto->presupuesto()->updateOrCreate(['id_proyecto' => $proyecto->id], $request->all());

    return response()->json($presupuesto->load('proyecto:id,estado_id'), 200);

  }

  public function updatePresupuesto($presupuesto_id, Request $request){

    $validator = Validator::make($request->all(), [
        'id_proyecto' => 'required',
        'descripcion' => 'required',
        'file' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error'=>$validator->errors()
        ], 422);
    }

    if($request->hasFile('file')){
        $fileName = uniqid() . '_' . $request->file->getClientOriginalName();

        Storage::disk('presupuesto')->put($fileName, File::get($request->file));

        $request['file_name'] = $fileName;
    }

    $count_proyecto = Proyecto::where('id', $request->id_proyecto)->count();

    if($count_proyecto == 0) {
        return response()->json('Proyecto no existe', 400);
    }

    $presupuesto = Presupuesto::find($presupuesto_id);

    if($presupuesto == null) {
        return response()->json('Presupuesto no existe', 400);
    }

   $presupuesto_actualizado = Presupuesto::where('id', $presupuesto_id)->update($request->except('file'));

    return response()->json($presupuesto_actualizado, 200);

  }

  public function sendMail($presupuesto_id, Request $request) {
      $presupuesto = Presupuesto::find($presupuesto_id);

      if($presupuesto == null) {
        return response()->json('No existe el presupuesto', 400);
      }

    Mail::to($request->email)->send(new PresupuestoMail($presupuesto));

    return response()->json('Enviado con exito', 200);
  }
}
