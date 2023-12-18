<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FichajePdfResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $body = collect(FichajeResource::collection($this->fichaje))->groupBy('fecha');
        $vacaciones = collect(VacacionResource::collection($this->Vacaciones))->groupBy('fecha');
        $contador_horas = $body->map(function ($x, $key){
            return count($x);
         })->max();
        $rango_horas = range(1, $contador_horas);
        $headers = collect($rango_horas)->map(function ($i){
            return "Fichaje {$i}";
          });
        return [
            'headers'      => $headers,
            'body'         => $body,
            'rango_horas'  => $rango_horas,
            'vacaciones'   => $vacaciones,
            'usuario' => $this->resource,

        ];
    }
   
}
