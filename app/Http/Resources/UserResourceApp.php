<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceApp extends JsonResource
{
  public $preserveKeys = true;
  public function toArray($request)
  {
    return [
        'id'              => $this->id,
        'nombre'          => $this->nombre,
        'nombre_fiscal'   => $this->nombre_fiscal,
        'cif'             => $this->cif,
        'telefono'        => $this->telefono,
        'email'           => $this->email,
        'direccion'       => $this->direccion,
        'codigo_postal'   => $this->codigo_postal,
        'localidad'       => $this->localidad,
        'provincia_id'    => $this->provincia_id,
        'provincia'       => $this->provincia->nombre,
    ];
  }
}