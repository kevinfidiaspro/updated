<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FichajeCreateResource extends JsonResource
{
    public function toArray($request)
    {
      return [
        'id'=>$this->id,

        'usuario_id'    => $this->usuario_id,
        'fecha' => Carbon::parse($this->fecha)->format('Y-m-d'),
        'hora'  => Carbon::parse($this->fecha)->format('H:i'),

      ];
    }
}
