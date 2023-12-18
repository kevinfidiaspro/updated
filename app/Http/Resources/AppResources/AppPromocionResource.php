<?php

namespace App\Http\Resources\AppResources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppPromocionResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id'      => $this->id,
      'nombre'  => $this->nombre,
      'url'    => $this->url
    ];
  }
}
