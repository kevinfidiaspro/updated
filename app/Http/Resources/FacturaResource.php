<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FacturaResource extends JsonResource
{
    public function toArray($request)
    {
      return [
        'id'          => $this->id,
        'cliente'     => $this->cliente ? $this->cliente->nombre : null,
        'nro_factura' => $this->nro_factura ? str_pad($this->nro_factura->nro_factura, 4, '0', STR_PAD_LEFT) : null,
        'fecha'       => Carbon::parse($this->fecha)->format('d-m-Y'),
        'total'       => "{$this->total}€",
        'nombre_factura' => $this->factura_url,
        'factura_path'   => "storage/recibos/{$this->factura_url}",
      ];
    }
}
