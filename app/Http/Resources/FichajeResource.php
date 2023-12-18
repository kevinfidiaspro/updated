<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FichajeResource extends JsonResource
{
    public function toArray($request)
    {
      return [
        'id'=>$this->id,
        'usuario_id'    => $this->usuario_id,
        'fecha_fichaje' => Carbon::parse($this->fecha)->format('d-m-Y'),
        'hora_fichaje'  => Carbon::parse($this->fecha)->format('H:i'),
        'fecha' => Carbon::parse($this->fecha)->format('Y-m-d'),
        'hora'  => Carbon::parse($this->fecha)->format('H:i'),
        $this->mergeWhen($this->usuario()->exists(), function() {
          return [
            'nombre' => $this->usuario->nombre,
          ];
        })
      ];
    }
}
