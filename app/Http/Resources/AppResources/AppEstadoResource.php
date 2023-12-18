<?php

namespace App\Http\Resources\AppResources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppEstadoResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id'           => $this->id,
      'descripcion'  => $this->descripcion,
      'fecha'        => $this->fecha,
      'finalizado'   => $this->finalizado
    ];
  }
}
