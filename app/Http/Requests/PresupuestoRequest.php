<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresupuestoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      return [
        'id_proyecto' => 'required',
        'descripcion' => 'required',
        'file_name' => 'required',
      ];
    }

    public function messages()
    {
        return [
          'id_proyecto.required'        => 'debe ingresar un proyecto',
          'descripcion.required' => 'la descripcion es obligatoria',
          'file_name.required' => 'el archivo es obligatorio',
        ];
    }
}
