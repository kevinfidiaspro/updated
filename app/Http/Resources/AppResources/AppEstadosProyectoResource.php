<?php

namespace App\Http\Resources\AppResources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AppResources\AppEstadoResource;

class AppEstadosProyectoResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id'               => $this->id,
      'estados_proyecto' => AppEstadoResource::collection($this->estadosProyecto)
    ];
  }
}
