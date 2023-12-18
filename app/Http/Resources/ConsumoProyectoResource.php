<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumoProyectoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /*
        { text: "Cliente", value: "cliente", sortable: false },
                { text: "Proyecto", value: "nombre", sortable: false },
                { text: "Tipo", value: "tipo", sortable: false },
                {
                    text: "Horas Estimadas",
                    value: "estimadas",
                    sortable: false,
                },
                { text: "Horas Reales", value: "reales", sortable: false },
                { text: "Diferencia", value: "diferencia", sortable: false },
        */ 
        $minutos = 0;
        foreach($this->Tareas as $tarea){
            $minutos += $tarea->tiempo;
        }
        return [
            'cliente'=>$this->usuario->nombre,
            'nombre'=>$this->nombre,
            'estimadas'=>$this->minutos_estimados,
            'reales'=>$minutos,
            'diferencia'=>$this->minutos_estimados - $minutos
        ];
    }
}
