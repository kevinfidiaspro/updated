<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Requests\Users\UserRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Models\Proyecto;
use App\Models\ProyectoEstado;

use App\Models\EstadoProyecto;
use App\Http\Requests\ProyectoRequest;
use App\Http\Resources\ProyectoResource;
use App\Http\Requests\PotencialRequest;
use App\Http\Resources\PotencialResource;
use App\Http\Resources\ConsumoProyectoResource;
use App\Models\Estado;
use Validator;
use App\Helpers\FileHelper;
use App\Models\ProyectoUsuarios;
use App\Models\EstadoPotencial;
use App\Models\Tareas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class ProyectoController extends Controller
{
  public function getMinutosUtilizados(Request $request){
    $proyecto = Proyecto::find($request->id_proyecto);
    
    if($proyecto == null){
      return ['tiempo'=>0,'asignados'=> 0,'semanal'=>1];

    }

    $now = Carbon::now();
    if($proyecto->semanal == 1){
      $weekStartDate = $now->startOfWeek()->format('Y-m-d');
      $weekEndDate = $now->endOfWeek()->format('Y-m-d');
      $now = $now->subDays(7);
      $pastWeekStartDate = $now->startOfWeek()->format('Y-m-d');
      $pastWeekEndDate = $now->endOfWeek()->format('Y-m-d');

      $tareas = Tareas::where('id_proyecto',$request->id_proyecto)->whereBetween('fecha',[$weekStartDate,$weekEndDate])->get();
      $tareas_pasado = Tareas::where('id_proyecto',$request->id_proyecto)->whereDate('fecha','>=','2023-08-01')->whereBetween('fecha',[$pastWeekStartDate,$pastWeekEndDate])->get();
    }else if ($proyecto->semanal == 2){
      $monthStartDate = $now->startOfMonth()->format('Y-m-d');
      $monthEndDate = $now->endOfMonth()->format('Y-m-d');

      $PastMonth = $now->startOfMonth()->subMonth(1)->format('m');
      $PastYear = $now->endOfMonth()->subMonth(1)->format('Y');

      $tareas = Tareas::where('id_proyecto',$request->id_proyecto)->whereBetween('fecha',[$monthStartDate,$monthEndDate])->get();
      $tareas_pasado = Tareas::where('id_proyecto',$request->id_proyecto)->whereMonth('fecha',$PastMonth)->whereYear('fecha',$PastYear)->whereDate('fecha','>=','2023-08-01')->get();

    }else{
      $tareas = Tareas::where('id_proyecto',$request->id_proyecto)->get();
      $tareas_pasado = null;

    }
   
    $tiempo = 0;
    $tiempo_pasado = 0;
    $asignados = 0;
    if($proyecto->minutos_estimados){
      $asignados = $proyecto->minutos_estimados;
    }
    foreach($tareas as $tarea){
      $tiempo += $tarea->tiempo;
    }
    if($tareas_pasado != null){
      foreach($tareas_pasado as $tarea){
        $tiempo_pasado += $tarea->tiempo;
      }
    }

    $tiempo_pasado = $asignados - $tiempo_pasado;
    if($tiempo_pasado >0) $tiempo_pasado = 0;
    return ['tiempo'=>$tiempo,'asignados'=> $asignados,'semanal'=>$proyecto->semanal,'pasado'=>$tiempo_pasado,'tareas_pasado'=>$tareas_pasado];
  }
  public function getEstados(){
    $estados = EstadoPotencial::all();
    return $estados;
  }
  public function SaveEstados(Request $request){
    $estado = EstadoPotencial::updateOrCreate(['id'=>$request->id],['nombre'=>$request->nombre]);
  }
  public function DeleteEstados($id){
    $estado = EstadoPotencial::find($id);
    $estado->delete();

  }
  public function getAllProyectos(){
    $proyecto = Proyecto::with('servicio','estado','usuario','presupuesto')
                          ->where('estado_id','2')
                          ->orderBy('fecha_alta', 'DESC')
                          ->get();
    return response()->json($proyecto, 200);
  }
  public function getProyectoByid($proyecto_id){
    $proyecto = Proyecto::with(['servicio', 'usuario', 'estadosProyecto.estado', 'archivos','usuarios.usuario','facturas','ingresos'])->find($proyecto_id);
    return response()->json($proyecto, 200);
  }
  public function getProyectosByUserId($userid, $tipo){
    
    if($tipo == 0){
      $proyecto = Proyecto::with('servicio','estado','usuario','presupuesto')
                ->where('estado_id', '=', '2')
                ->where('usuario_id', '=', $userid)
                ->where('activo', '=', true)
                ->orderBy('fecha_alta', 'DESC')->get();
    }else if ($tipo == 1){
      $proyecto = Proyecto::with('servicio','estado','usuario','presupuesto')
                ->where('estado_id', '=', '2')
                ->where('usuario_id', '=', $userid)
                ->where('activo', '=', false)
                ->orderBy('fecha_alta', 'DESC')->get();
    }else{
      $proyecto = Proyecto::with('servicio','estado','usuario','presupuesto')
      ->where('estado_id', '=', '2')
      ->where('usuario_id', '=', $userid)
      ->orderBy('fecha_alta', 'DESC')->get();
    }
    
    return response()->json($proyecto, 200);
  }
  public function deleteProyecto($proyecto_id){
    $proyecto = Proyecto::find($proyecto_id);
    $proyecto->delete();
    return response()->json($proyecto, 200);
  }
  public function saveProyecto(ProyectoRequest $request){
    $proyecto = json_decode($request->proyecto, true);
    $itemsEstado = json_decode($request->itemsEstado, true);    
    $idItemsEstado = json_decode($request->idItemsEstado, true);
    $activo = $proyecto['activo'];
    $idUser = $proyecto['usuario']['id'];
    $usuario = $proyecto['usuario'];
    if($idUser == null){
      $user = new User();
      $user->fill($usuario);
      $password            = Str::random(10);
      $user->password      = Hash::make($password);
      $user->role          = 2;
      $user->saveOrFail();
    }else{
      $user = User::where('id', '=', $idUser)->first();
      $user->fill($usuario);
      $user->role = 2;
      $user->update();
    }
    $proyecto = Proyecto::updateOrcreate(['id' => $proyecto['id']], [
      'usuario_id'      => $user->id,
      'servicio_id'     => $proyecto['servicio_id'],
      'fecha_alta'      => $proyecto['fecha_alta'],
      'detalle_servicio'=> $proyecto['detalle_servicio'],
      'estado_id'       => $proyecto['estado_id'],
      'pvp'             => $proyecto['pvp'],
      'pvp_gasto'       => $proyecto['pvp_gasto'],
      'detalles_gasto'  => $proyecto['detalles_gasto'],
      'nombre'          => $proyecto['nombre'],
      'porc_realizado'  => $proyecto['porc_realizado'],
      'activo'          => $proyecto['activo'],
      'minutos_estimados'          => $proyecto['minutos_estimados'],
      'semanal' => $proyecto['semanal']??1,
      'tipo_proyecto'=>$proyecto['tipo_proyecto'],
      'no_mail'=>$proyecto['no_mail']??0,
      'es_kit'=>$proyecto['es_kit']??0
    ]);
    $this->saveProyectoUsuario($request,$proyecto);
    //save or update the project states corregir
    $items = [];
    if($itemsEstado != []) {
        foreach($itemsEstado as $key => $item) {
            $proyecto->estadosProyecto()->updateOrCreate(
                ['id' => $idItemsEstado[$key]],
                [
                  'id_estado' => $item['id_estado']??0,

                  'descripcion' => $item['descripcion']??'',
                  'fecha'       => $item['fecha'],
                  'finalizado'  => $item['finalizado']
                ]
            );
            array_push($items, $item);
        }
        //app('App\Http\Controllers\ApiControllers\FCMNotificationController')->SendUserNotification($idUser,"Estado del proyecto actualizado","el estado de su proyecto a cambiado: ","estados");
      }
    if($request->hasFile('itemsFiles')){
       $fileHelper = new FileHelper($proyecto);
       $fileHelper->guardarArchivos($request->itemsFiles, 'potencial');
    }
    $proyecto->load(['servicio', 'usuario', 'estadosProyecto', 'archivos']);
    return response()->json(['proyecto' => $proyecto], 200);
  }
  public function getAllPotenciales(){
    $potencial = Proyecto::with('servicio','estado','usuario', 'presupuesto','LeadForm','EstadoPotencial')->where('estado_id', '=', '3')->orderBy('fecha_alta', 'DESC')->get();
    return response()->json($potencial, 200);
  }
  public function getProyectosFiltered(Request $request,$tipo){
    $potencial = Proyecto::select('proyecto.*','estado_potenciales.nombre as estado_nombre','estado_potenciales.id as estado_id')->with('servicio','estado','usuario.Vendedor', 'presupuesto','LeadForm','EstadoPotencial','lastActiveState.estado','ProyectoTareas')
    ->join('estado_potenciales','estado_potenciales.id','=','proyecto.id_estado_potencial')
    ->where(function ($query) use ($request,$tipo){
      if($tipo == 3 || $tipo == 2){
        $query->where('estado_id', '=', $tipo);
      }
      
        if($request->datestart!="" && $request->dateend!=""){
          $query->whereBetween('fecha_alta', [date('Y-m-d', strtotime($request->datestart)), date('Y-m-d', strtotime($request->dateend))]);
        }else if($request->datestart!=""){
          $query->where('fecha_alta', '>=', [date('Y-m-d', strtotime($request->datestart))]);
        }else if ($request->dateend!=""){
          $query->where('fecha_alta', '<=', [date('Y-m-d', strtotime($request->dateend))]);
        }
        if($request->estado){$query->where('id_estado_potencial', $request->estado);}
        if($request->campain){$query->where('id_lead_form', $request->campain);}
    });
    if($request->activo != null){
      $potencial->where('activo',$request->activo);
    }
    if($request->semana != null){
        $potencial->where('semanal',$request->semana);
    
    }
    if($request->servicio){
        $potencial->where('servicio_id',$request->servicio);
    }
    $user= $request->user();
    if($user->role == 9  || $user->role == 8){
      $potencial->whereHas('usuario',function ($query) use ($request,$user){
        $query->where('vendedor_id',$user->id);
      });
    }
    if($request->search){
      $potencial->where(function($query)use($request){

        $query->whereHas('usuario',function ($query) use ($request){
          
          $query->where('nombre','LIKE','%'.$request->search.'%');
          $query->orWhere('nombre_fiscal','LIKE','%'.$request->search.'%');
          $query->orWhere('nombre_comercial','LIKE','%'.$request->search.'%');
          $query->orWhere('email','LIKE','%'.$request->search.'%');
          $query->orWhere('telefono','LIKE','%'.$request->search.'%');
          $query->orWhere('fecha_alta','LIKE','%'.$request->search.'%');
        });
        $query->orWhere('usuario_id','LIKE','%'.$request->search.'%');
        $query->orWhere('proyecto.nombre','LIKE','%'.$request->search.'%');
        $query->orWhereHas('servicio',function ($query) use ($request){
          $query->where('nombre','LIKE','%'.$request->search.'%');
        });
        $query->orWhereHas('estado',function ($query) use ($request){
          $query->where('nombre','LIKE','%'.$request->search.'%');
        });
      });
      
    }
    if($request->sortBy == null){
      $potencial->orderBy('created_at', 'DESC');
    }
    else{
      if($request->sortBy == 'es_kit'){
        $potencial->orderBy('es_kit', $request->sortDesc?'DESC':'ASC');
    }
      else if($request->sortBy == 'estado_potencial'){
        $potencial->orderBy('estado_potenciales.nombre',$request->sortDesc?'DESC':'ASC');
      }else{
        $potencial->orderBy('created_at', 'DESC');

      }
    }
    if($request->itemsPerPage == -1){
      $data = $potencial->get();
      $potencial = ['total'=>count($data),'data'=>$data];
    }
    else{
      $potencial = $potencial->paginate($request->itemsPerPage??15);
    }
    return $potencial;
  }
  public function getAllProyectosCliente(Request $request){
return $this->getProyectosFiltered($request,2);
  }
  public function getAllPotencialesFiltro(Request $request){
   
    return $this->getProyectosFiltered($request,3);
  }
  public function getPotencialByid($potencial_id){
    $potencial = Proyecto::with(['servicio','usuario', 'archivos','LeadForm','EstadoPotencial'])->find($potencial_id);
    return response()->json($potencial, 200);
  }
  public function deletePotencial($potencial_id){
    $potencial = Proyecto::find($potencial_id);
    $potencial->delete();
    return response()->json($potencial, 200);
  }
  public function savePotencial(PotencialRequest $request){
    return DB::transaction(function () use ($request) {
    $potencial =  json_decode($request->potencial, true);
    $usuario = $potencial['usuario'];
    $idUser = $usuario['id'];

    // var_dump($potencial);
    // var_dump($usuario);
    // var_dump($idUser);
    // die();
    // die();
    // die();

    if($idUser == null){
      $user = new User();
      $user->fill($usuario);
      $password            = Str::random(10);
      $user->password      = Hash::make($password);
      $user->saveOrFail();
    }else{
      $user = User::where('id', '=', $idUser)->first();
      $user->fill($usuario);
      $user->update();
    }
    
    if ($potencial['estado_id'] == 2) {
      $potencial_guardado = Proyecto::updateOrcreate(['id' => $potencial['id']], [
        'usuario_id'            => $user->id,
        'servicio_id'           => $potencial['servicio_id'],
        'fecha_alta'            => $potencial['fecha_alta'],
        'detalle_servicio'      => $potencial['detalle_servicio'],
        'estado_id'             => $potencial['estado_id'],
        'pvp'                   => $potencial['pvp'],
        'pvp_gasto'             => $potencial['pvp_gasto'],
        'detalles_gasto'        => $potencial['detalles_gasto'],
        'id_estado_potencial'   => $potencial['id_estado_potencial'] ?? 1,
        'activo'                => 1,
        'id_lead_form'          => $potencial['id_lead_form'],
        'tipo_proyecto'=>$potencial['tipo_proyecto'],
        'es_kit'=>$potencial['es_kit']??0

      ]);
    }
    else
    {
      $potencial_guardado = Proyecto::updateOrcreate(['id' => $potencial['id']], [
        'usuario_id'            => $user->id,
        'servicio_id'           => $potencial['servicio_id'],
        'fecha_alta'            => $potencial['fecha_alta'],
        'detalle_servicio'      => $potencial['detalle_servicio'],
        'estado_id'             => $potencial['estado_id'],
        'pvp'                   => $potencial['pvp'],
        'pvp_gasto'             => $potencial['pvp_gasto'],
        'detalles_gasto'        => $potencial['detalles_gasto'],
        'id_estado_potencial'   => $potencial['id_estado_potencial'] ?? 1,
        'id_lead_form'          => $potencial['id_lead_form'],

        'activo'                => 0,
        'tipo_proyecto'=>$potencial['tipo_proyecto'],
        'es_kit'=>$potencial['es_kit']??0

      ]);
    }

    if($request->hasFile('itemsFiles')){
       $fileHelper = new FileHelper($potencial_guardado);
       $fileHelper->guardarArchivos($request->itemsFiles, 'potencial');
    }

    return response()->json(['potencial' => $potencial_guardado, 'user' => $user], 200);
  });
  }


  public function getProyectoEstados(){
    return ProyectoEstado::all();
  }
  public function saveProyectoEstado(Request $request){
    return ProyectoEstado::updateOrCreate(['id'=>$request->id],$request->all());
  }
  public function deleteProyectoEstado(Request $request){
    return ProyectoEstado::find($request->id)->delete();
  }
  public function updateProyectoEstado($proyecto_estado_id) {
    $estado = EstadoProyecto::find($proyecto_estado_id);
    $estado->finalizado = !$estado->finalizado;
    $estado->save();
    return response()->json('Estado actualizado', 200);
  }
  public function deleteEstadoProyecto($proyecto_estado_id) {
    $estado = EstadoProyecto::find($proyecto_estado_id);
    $estado->delete();
    return response()->json($estado, 200);
  }
  public function deleteFile($tipo, $id){
    $fileHelper = new FileHelper(new Proyecto());
    $fileHelper->deleteFile($id, $tipo);
    return response()->json(['status' => 'success'], 200);
  }
  public function getConsumoTareas(Request $request){
    $mes_exp = explode('-',$request->mes??date('Y-m-d'));
    $month = $mes_exp[1];
    $year = $mes_exp[0];
    $proyectos = Proyecto::withWhereHas('Tareas',function($query) use ($month,$year){
      $query->whereMonth('fecha',$month)->whereYear('fecha',$year);
    });
    if($request->id_usuario){
      $proyectos->whereHas('usuarios', function($query) use ($request){
        $query->where('id_usuario',$request->id_usuario);
      });
    }
    $proyectos = $proyectos->get();
    return ConsumoProyectoResource::collection($proyectos);
  }
  public function getProyectosActivos(Request $request){
    $proyectos = Proyecto::join('users', 'proyecto.usuario_id', '=', 'users.id')->select('proyecto.*')
    ->selectRaw("CONCAT(proyecto.nombre, ' - ', users.nombre_fiscal) AS nombre_completo");
    $user = $request->user();
    if($user->role != 1){
      $proyectos->where('activo',1)->whereHas('usuarios', function($q) use($user){
        $q->where('id_usuario',$user->id);
      })->get();
    }
    
    return $proyectos->get();
  }
  public function getProyectosByUser(Request $request){
    $user = $request->user();
    $proyectos = Proyecto::where('activo',1)->whereHas('usuarios', function($q) use($user){
      $q->where('id_usuario',$user->id);
    })->get();
    return $proyectos;
  }
  private function saveProyectoUsuario(Request $request,$proyecto){
    if($request->usuarios && $proyecto !=null){
      $ids = [];
      foreach(json_decode($request->usuarios) as $usuario){
        $rel = ProyectoUsuarios::updateOrCreate(['id_proyecto'=>$proyecto->id,'id_usuario'=>$usuario->id_usuario],['id_proyecto'=>$proyecto->id,'id_usuario'=>$usuario->id_usuario]);
        $ids[]=$usuario->id_usuario;
      }
      ProyectoUsuarios::where('id_proyecto',$proyecto->id)->whereNotIn('id_usuario',$ids)->delete();
    }
  }
}
