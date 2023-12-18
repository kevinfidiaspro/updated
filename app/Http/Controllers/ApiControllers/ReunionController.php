<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\Reunion;
use App\Models\ReunionAsistente;
use Validator;

use Illuminate\Support\Facades\DB;
class ReunionController extends Controller
{
    public function GetReunion(Request $request){
        $tarea = Reunion::with(['Asistentes.Asistente'])->find($request->id);
        return $tarea;
    }
    public function SaveAsistentes($reunion,$asistentes){
        $ids = [];
        foreach($asistentes as $asistente){
            $reu_asis = ReunionAsistente::updateOrCreate(['id'=>$asistente['id']??null],
            [
                'id_reunion'=>$reunion->id,
                'id_asistente'=>$asistente['id_asistente'],
            ]);
            $ids[] = $reu_asis->id;
        }
        $elementos = ReunionAsistente::where('id_reunion',$reunion->id)->whereNotIn('id',$ids)->delete();
    }
    public function SaveReunion(Request $request){
        $validator = Validator::make($request->all(), [
            'hora_desde' => 'required',
            'hora_hasta' => 'required',

            'fecha' => 'required',
            'comentario'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors()
            ], 422);
        }
        $tarea = Reunion::updateOrCreate(['id'=>$request->id],[
            'id_organizador'=>$request->user()->id,
            'hora_desde'=>$request->hora_desde,
            'hora_hasta'=>$request->hora_hasta,
            'fecha'=>$request->fecha,
            'comentario'=>$request->comentario,
        ]);
        $this->SaveAsistentes($tarea,$request->asistentes);
    }
    public function cancelReunion(Request $request){
        $tarea = Reunion::find($request->id)->delete();
    }
  
 
    public function GetReuniones(Request $request){
        $user = $request->user();
        $tareas = Reunion::with(['Organizador','Asistentes'])->get();
      
        return $tareas;
    }
}
