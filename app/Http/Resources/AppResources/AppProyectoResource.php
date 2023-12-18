<?php

namespace App\Http\Resources\AppResources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AppResources\AppEstadoResource;

class AppProyectoResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id'             => $this->id,
      'porc_realizado' => $this->porc_realizado,
      'fecha_alta'     => $this->fecha_alta,
      'nombre'         => $this->nombre,
      $this->mergeWhen($this->servicio()->exists(), function() {
        return [
          'servicio' => [
            'nombre'  => $this->servicio->nombre
          ]
        ];
      })
    ];
  }
}
