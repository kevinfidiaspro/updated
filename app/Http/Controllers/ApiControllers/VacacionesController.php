<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\Vacaciones;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\VacacionResource;
class VacacionesController extends Controller
{
    public function getVacaciones(Request $request){
        $vacaciones = Vacaciones::where('id_usuario',$request->id_usuario)->whereYear('fecha',$request->year)->orderBy('fecha')->get();
        return VacacionResource::collection($vacaciones);
    }
    public function getVacacionesCalendario($fecha_desde,$fecha_hasta){
        return Vacaciones::with('Usuario')->whereDate('fecha','<=',$fecha_hasta)->whereDate('fecha','>=',$fecha_desde)->orderBy('fecha')->get();
    }
    public function getVacacionesYear($year){
        $vacaciones =  Vacaciones::with('Usuario')->whereYear('fecha',$year)->orderBy('fecha')->get();
        $result = [];
        foreach($vacaciones as $vacacion){
            if(isset($result[$vacacion->fecha])) $result[$vacacion->fecha] = [];
            $result[$vacacion->fecha][] = [
                'id_usuario'=>$vacacion->id_usuario,
                'color'=>$vacacion?->Usuario?->color??'#000000',
                'nombre'=>$vacacion?->Usuario?->nombre??''
            ];
        }
        return $result;
    }
    public function saveVacaciones(Request $request){
        foreach($request->vacaciones as $fecha){
            $vacaciones = Vacaciones::updateOrCreate(['fecha'=>$fecha,'id_usuario'=>$request->id_usuario],['fecha'=>$fecha,'id_usuario'=>$request->id_usuario]);
        }
    }
    public function deleteVacaciones($id){
        Vacaciones::find($id)->delete();
    }
}
