<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProyectoResource extends JsonResource
{
  public $preserveKeys = true;
  public function toArray($request)
  {
    return [
       
      'id'                    => $this->id,
      'fecha_alta'            => $this->fecha_alta,
      'detalle_servicio'      => $this->detalle_servicio,
      'estado_id'             => $this->estado->id,
      'pvp'                   => $this->pvp,
      'pvp_gasto'             => $this->pvp_gasto,
      'detalles_gasto'        => $this->detalles_gasto,      
      'activo'                => $this->activo,      
        
      'usuario.id'            => $this->usuario->id,
      'usuario.user_id'       => $this->usuario->user_id,
      'usuario.nombre'        => $this->usuario->nombre,
      'usuario.nombre_fiscal' => $this->usuario->nombre_fiscal,
      'usuario.dni'           => $this->usuario->dni,
      'usuario.telefono'      => $this->usuario->telefono,
      'usuario.email'         => $this->usuario->email,
      'usuario.direccion'     => $this->usuario->direccion,
      'usuario.codigo_postal' => $this->usuario->codigo_postal,
      'usuario.localidad'     => $this->usuario->localidad,
      'usuario.provincia_id'  => $this->usuario->provincia_id,
      'usuario.cuenta'        => $this->usuario->cuenta,
      'usuario.fecha_alta'    => $this->usuario->fecha_alta,
      'usuario.observaciones' => $this->usuario->observaciones,

      'estado_id'             => $this->estado->id,                  
      'estado.nombre'         => $this->estado->nombre, 

      'servicio_id'           => $this->servicio->id,              
      'servicio.nombre'       => $this->servicio->nombre,
    ];
  }
}
