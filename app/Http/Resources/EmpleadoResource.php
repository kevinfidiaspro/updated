<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class EmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $vacaciones =0;
        if($this->fecha_alta){
            $this_month = Carbon::parse($this->fecha_alta)->floorMonth(); // returns 2019-07-01
            $start_month = Carbon::parse(date('Y-m-d'))->floorMonth(); // returns 2019-06-01
            $vacaciones = ($start_month->diffInMonths($this_month)+1)*2.5; 
            $vacaciones -= $this->Vacaciones()->count();
        }
        $roles = ['','Administrador','Cliente','Empleado','Potencial','Administración','Marketing','Supervisor Marketing','Atención al cliente','Ventas'];

        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'nombres' =>$this->nombres,
            'nombre'=>$this->nombre,
            'nombre_fiscal'=>$this->nombre_fiscal,
            'nombre_comercial'=>$this->nombre_comercial,
            'cif'=>$this->cif,
            'telefono'=>$this->telefono,
            'email'=>$this->email,
            'direccion'=>$this->direccion,
            'codigo_postal'=>$this->codigo_postal,
            'localidad'=>$this->localidad,
            'provincia_id'=>$this->provincia_id,
            'cuenta'=>$this->cuenta,
            'fecha_alta'=>$this->fecha_alta,
            'observaciones'=>$this->observaciones,
            'role'=>$this->role,
            'avatar'=>$this->avatar,
            'token_redes'=>$this->token_redes,
            'token_fichaje'=>$this->token_fichaje,
            'naf'=>$this->naf,
            'role_tfg'=>$this->rol_tfg ,
            'vendedor_id'=>$this->vendedor_id,
            'dias_vacaciones'=>$vacaciones,
            'vacaciones'=>$this->vacaciones,
            'role_str'=>$roles[$this->role],
            'role'=>$this->role,
            'color'=>$this->color,
        ];
    }
}
