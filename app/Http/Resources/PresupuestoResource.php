<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PresupuestoResource extends JsonResource
{
    public function toArray($request)
    {
      return [
        'id'      => $this->id,
        'cliente' => $this->cliente ? $this->cliente->nombre : null,
        'nro_presupuesto' => $this->nro_presupuesto ? str_pad($this->nro_presupuesto->nro_presupuesto, 4, '0', STR_PAD_LEFT) : null,
        'fecha'   => $this->fecha,
        'total'   => "{$this->total}€",
        'nombre_presupuesto' => $this->presupuesto_url,
        'presupuesto_path'   => "storage/recibos/{$this->presupuesto_url}",
      ];
    }
}
