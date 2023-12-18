<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotaResource extends JsonResource
{
    public function toArray($request)
    {
      return [
        'id'           => $this->id,
        'cliente'      => $this->cliente ? $this->cliente->nombre : null,
        'nro_nota'     => $this->nro_nota ? str_pad($this->nro_nota->nro_nota, 4, '0', STR_PAD_LEFT) : null,
        'fecha'        => $this->fecha,
        'total'        => "{$this->total}€",
        'nombre_nota'  => $this->nota_url,
        'nota_path' => "storage/recibos/{$this->nota_url}",
      ];
    }
}
