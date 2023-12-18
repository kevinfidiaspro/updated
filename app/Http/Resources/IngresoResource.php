<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IngresoResource extends JsonResource
{
    public function toArray($request)
    {
      return [
        'id'=>$this->id,
        'codigo'=>$this->codigo,
        'importe'=>$this->importe,
        'descripcion'=>$this->descripcion,
        'created_at'=>$this->created_at,
        'user_id'=>$this->user_id,
        'proyecto_id'=>$this->proyecto_id,
        'cliente_id'=>$this->cliente_id,
        'factura_id'=>$this->factura_id,
          'created_at'=>$this->created_at->toDateString(),
        'proyecto'=>$this->proyecto,
        'facturas'=>$this->facturas,
        'cliente_nombre'=>$this->cliente_nombre,
        'cliente'=>$this->cliente,
      ];
    }
}
