<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlbaranResource extends JsonResource
{
  public $preserveKeys = true;

  public function toArray($request)
  {
    return [
        'id'           => $this->id,
        'proveedor_id' => $this->proveedor_id,
        'proveedor'    => $this->proveedor ? $this->proveedor->nombre : null,
        'fecha'        => $this->fecha->format('d-m-Y'),
        'descripcion'  => $this->descripcion,
        'imagen'       => $this->imagen,
        'pdf'       => $this->pdf,
        'path'         => "storage/albaranes/{$this->imagen}",
        'imagen_name'  => substr($this->imagen, strpos($this->imagen, '-') + 1)
    ];
  }
}
