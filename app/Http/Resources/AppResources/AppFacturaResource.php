<?php

namespace App\Http\Resources\AppResources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppFacturaResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id_proyecto'  => $this->id_proyecto,
      'nro_factura'  => $this->nro_factura,
      'url'          => $this->url
    ];
  }
}
