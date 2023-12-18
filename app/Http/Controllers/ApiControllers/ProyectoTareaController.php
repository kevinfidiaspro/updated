<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\ProyectoTarea;
use Validator;
use Illuminate\Support\Facades\DB;
class ProyectoTareaController extends Controller
{
    public function GetTarea(Request $request){
        $tarea = ProyectoTarea::find($request->id);
        return $tarea;
    }
    public function SaveTarea(Request $request){
        $validator = Validator::make($request->all(), [
            'id_proyecto' => 'required',
            'fecha' => 'required',
            'comentario'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors()
            ], 422);
        }
        $tarea = ProyectoTarea::updateOrCreate(['id'=>$request->id],[
            'id_proyecto'=>$request->id_proyecto,
            'fecha'=>$request->fecha,
            'comentario'=>$request->comentario,
            'hora'=>$request->hora,
            'alarma'=>$request->alarma
        ]);

    }
    public function CancelTarea(Request $request){
        $tarea = ProyectoTarea::find($request->id)->delete();
    }
    public function GetProyectosForTareas(Request $request){
        $user = $request->user();

       /* if($user->role != 1){
            $proyectos = Proyecto::where('usuario_id',$user->id)->get();
        }
        else{*/
            $proyectos = Proyecto::all();
        //}
        return $proyectos;
      
    }
    public function GetTareasUnicas(Request $request){
        $user = $request->user();
        $tareas = ProyectoTarea::select(DB::raw('t1.id_proyecto,MAX(t1.id) AS id,MAX(t1.fecha) AS fecha,MAX(t1.comentario) AS comentario,MAX(t1.created_at) AS created_at,MAX(t1.updated_at) AS updated_at,MAX(t1.hora) AS hora'))
                                                ->from(DB::raw('proyecto_tarea AS t1 JOIN (SELECT id_proyecto, MAX(fecha) AS max_fecha FROM proyecto_tarea GROUP BY id_proyecto ) AS t2 ON t1.id_proyecto = t2.id_proyecto AND t1.fecha = t2.max_fecha'))
      
                                                ->with(['Proyecto.usuario']);
      
        if($request->cliente){
            $tareas->where('id_proyecto',$request->cliente);
        }
       /* if($request->desde){
            $tareas->where('fecha','>=',"'".$request->desde."'");
        }
        if($request->hasta){
            $tareas->where('fecha','<=',"'".$request->hasta."'");
        }*/
        $tareas->groupBy('id_proyecto');
        $tareas = $tareas->get();
        $result = [];
       
        foreach($tareas as $tarea){
            $add = true;
            if($user->role == 9  || $user->role == 8){
                if($tarea?->Proyecto?->Usuario?->vendedor_id){
                    if($tarea?->Proyecto?->Usuario?->vendedor_id != $user->id){
                        $add = false;
                    }
                }
                else{
                    $add = false;
                }
            }
            if($add){
                if($request->potencial_only){
                    $add = $tarea?->Proyecto?->estado_id == 3;
                }
                if($request->cliente_only){
                    $add = $tarea?->Proyecto?->estado_id != 3;
                }
            }
            
            if($add){
                $result[] = $tarea;
            }
        }
            /*$tareas->whereHas('Proyecto',function($query) use($user){
                $query->whereHas('usuario',function ($query) use ($user){
                $query->where('vendedor_id',$user->id);
                });
            });*/
        
    

        return $result;
    }
    public function GetTareas(Request $request){
        $user = $request->user();
        $tareas = ProyectoTarea::with(['Proyecto.usuario']);
        /*if($user->role != 1&&false){
            $tareas->whereHas('Proyecto',function($query) use($user){
                $query->where('usuario_id',$user->id);
            });
        }*/
        if($request->cliente){
        $tareas->where('id_proyecto',$request->cliente);
        }
        if($request->fecha_inicio) $tareas->whereDate('fecha','>=',$request->fecha_inicio);
        if($request->fecha_fin) $tareas->whereDate('fecha','<=',$request->fecha_fin);
        if($request->potencial_only){
            $tareas->whereHas('Proyecto',function($query) {
                $query->where('estado_id',3);
            });
        }
        if($request->cliente_only){
            $tareas->whereHas('Proyecto',function($query) {
                $query->where('estado_id','!=',3);
            });
        }
        $tareas = $tareas->get();
        return $tareas;
    }
}
