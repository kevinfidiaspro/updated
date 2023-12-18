<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tareas;
use App\Models\User;
use App\Exports\TareasExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Proyecto;
use App\Traits\Basic;
use Carbon\Carbon;
use App\Http\Resources\FichajeResource;
use App\Mail\HorasExcedidasMail;

use Illuminate\Support\Facades\Mail;
class TareasController extends Controller
{
    public function saveTarea(Request $request){
        $minutos = app('App\Http\Controllers\ApiControllers\ProyectoController')->getMinutosUtilizados($request);
        $proyecto = Proyecto::find($request->id_proyecto);
        
        $tarea = Tareas::updateOrCreate(['id' => $request->id], $request->all());
        
        $tareas = Tareas::where('id_usuario', $request->id_usuario)->where('fecha', $request->fecha)->get();
        
        if($proyecto->no_mail == 0&floatval( $minutos['asignados'])-floatval($minutos['tiempo']) + floatval($minutos['pasado'])<0){
           // $minutos['tiempo'] +=floatval($request->tiempo);
            Mail::to('franciscorg@fidiaspro.com')->send(new HorasExcedidasMail($proyecto, $minutos));
        }

        return response()->json($tareas, 200);
    }

    public function getTareas(Request $request){
        $tareas = Tareas::where('id_usuario', $request->id_usuario)->where('fecha', $request->fecha)->get();
  
        return response()->json($tareas, 200);
    }
    public function exportarTareas(Request $request){
        $tareas = Tareas::selectRaw('tareas.id_proyecto,tareas.id_usuario, sum(tareas.tiempo) as  tiempo_filtro')->whereBetween('tareas.fecha', [$request->fecha_inicio, $request->fecha_fin])
        ->withSum('Tareas','tiempo')
        ->where(function ($query) use ($request) {
            if($request->usuario!=null){
                $query->where('tareas.id_usuario', $request->usuario); 
            }
            if($request->proyecto!=null){
                $query->where('tareas.id_proyecto', $request->proyecto); 
            }
        })->groupBy('id_proyecto')
        ->get();
        return Excel::download(new TareasExport($tareas), 'Tareas.xlsx');

        return response()->json($tareas, 200);
    }
    public function buscarTareas(Request $request){
        $tareas = Tareas::whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin])
        ->orderBy('fecha', 'ASC')
        ->where(function ($query) use ($request) {
            $user = $request->user();
            if($user->role != 1 && $user->role !=  7){
                $query->where('id_usuario',$user->id);
            }else{
                if($request->usuario!=null){
                    $query->where('id_usuario', $request->usuario); 
                }
            }
            
            if($request->proyecto!=null){
                $query->where('id_proyecto', $request->proyecto); 
            }
        })
        ->get();

        return response()->json($tareas, 200);
    }

    public function eliminarTarea($tarea_id){
        $tarea = Tareas::find($tarea_id);
        $tarea->delete();
        return response()->json($tarea, 200);
    }
}