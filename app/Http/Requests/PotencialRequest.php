<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PotencialRequest extends FormRequest
{
    public function validationData(){
      return json_decode($this->potencial, true);
    }

    public function authorize(){
        return true;
    }

    public function rules(){
       $potencial = $this->validationData();
       
        return [
          'servicio_id'           => 'required',
          'fecha_alta'            => 'required',
          'estado_id'             => 'required',
          'pvp'                   => 'required',
          'usuario.nombre'        => 'required',
          // 'usuario.cif'           => 'required',
          'usuario.telefono'      => 'required',
          'usuario.email'         => 'required',
          // 'usuario.cuenta'        => 'required',
          // 'usuario.nombre_fiscal' => 'required',
        ];
    }

    public function messages()
    {
        return [
          'servicio_id.required'            => 'El Servicio es obligatorio',
          'fecha_alta.required'             => 'La fecha de alta es obligatoria',
          'estado_id.required'              => 'El estado es obligatorio',
          'pvp.required'                    => 'El Precio es obligatorio',
          'usuario.nombre.required'         => 'El Nombre es obligatorio',
          // 'usuario.nombre_fiscal.required'  => 'El Nombre Fiscal es obligatorio',
          // 'usuario.cif.required'            => 'El CIF / DNI es obligatorio',
          'usuario.telefono.required'       => 'El Telefono es obligatorio',
          'usuario.email.required'          => 'El Email es obligatorio', 
          // 'usuario.cuenta.required'         => 'La cuenta es obligatoria', 
        ];
    }
}
