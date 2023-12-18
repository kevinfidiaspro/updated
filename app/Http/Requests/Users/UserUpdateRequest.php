<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            // 'nombre' => 'required',
            // 'nombre_fiscal' => 'required',
            // 'cif' => 'required|max:9',
            // 'telefono' => 'required|max:9',
            // 'email' => 'required|unique:users',
            // 'role' => 'required',
            // 'provincia_id' => 'required', 
            // 'localidad' => 'required|max:60',
            // 'codigo_postal' => 'numeric|digits_between:1,5|nullable',
        ];
    }

    public function messages(){
        return [
            'nombre.required' => 'El campo nombre es requerido',
            // 'nombre_fiscal.required' => 'El campo nombre fiscal es requerido',
            // 'cif.required' => 'El campo cif es requerido',
            'cif.max' => 'El campo cif solo puede tener 9 caracteres',
            'telefono.max' => 'El campo telefono solo puede tener 9 caracteres',
            'telefono.required' => 'El campo telefono es requerido',
            'email.required' => 'El campo email es requerido',
            'role.required' => 'El campo rol es requerido',
            'provincia_id.required' => 'El campo provincia es requerido',
            'localidad.required' => 'El campo localidad es requerido',
            'localidad.max' => 'El campo localidad solo puede tener 60 caracteres',
            'codigo_postal.numeric' => 'Codigo postal debe ser numerico',
        ];
    }
}