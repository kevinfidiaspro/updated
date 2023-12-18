<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TicketsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "descripcion" => $this->descripcion,
            "estado" => isset($this->estadoTicket) ? $this->estadoTicket->descripcion : 'Sin información',
            "departamento" => isset($this->departamento) ? $this->departamento->descripcion : 'Sin información',
            "created_at" => $this->created_at->format('d/m/Y'),
            "fecha_finalizacion" => isset($this->fecha_finalizacion) ? Carbon::parse($this->fecha_finalizacion)->format('d/m/Y') : 'Sin información',
            "cliente" => isset($this->user->nombre) ? $this->user->nombre : 'Sin información',
            "responsable" => isset($this->responsable->nombre) ? $this->responsable->nombre : 'No ha sido asignado',
            "id_urgencia" => $this->id_urgencia,
            "urgencia" => isset($this->urgencia) ? $this->urgencia->descripcion : 'Sin información'
        ];
    }
}
