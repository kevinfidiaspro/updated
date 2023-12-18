<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    public function saveEmpresa(Request $request){
        return Empresa::updateOrCreate(['id'=>$request->id],$request->all());
    }
    public function getEmpresa($id){
        return Empresa::find($id);
    }
    public function getEmpresas(){
        return Empresa::all();
    }
    public function deleteEmpresa($id){
        Empresa::find($id)->delete();
        return $this->getEmpresas();
    }
}
