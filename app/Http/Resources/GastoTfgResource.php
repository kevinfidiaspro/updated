<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GastoTfgResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $month = $months[intval (explode('-',$this->mes)[1])-1];
        return [
            'id'=>$this->id,
            'descripcion'=>$this->descripcion,
            'id_tipo'=>$this->id_tipo,
            'mes'=>$this->mes,
            'mes_str'=>$month,
            'euros'=>$this->euros,
            'tipo'=>$this->Tipo,
        ];
    }
}
