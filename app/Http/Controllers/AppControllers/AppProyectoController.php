<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Resources\AppResources\AppProyectoResource;
use App\Http\Resources\AppResources\AppEstadosProyectoResource as EstadoResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyecto;

class AppProyectoController extends Controller
{
  public function getProyectosByUserId($user_id){
    $proyectos = Proyecto::with('servicio:id,nombre')->where('usuario_id', $user_id)->get();
    $proyecto_resource = AppProyectoResource::collection($proyectos);
    return response()->json($proyecto_resource, 200);
  }

  public function getEstadoByProyectoId($proyecto_id){
    $proyecto = Proyecto::with('estadosProyecto')->where('id', $proyecto_id)->get()->first();
    $estado_resource = new EstadoResource($proyecto);
    return response()->json($estado_resource, 200);
  }

}
