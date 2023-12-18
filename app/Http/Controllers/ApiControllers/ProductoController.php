<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\ProductoModulo;
use Validator;

class ProductoController extends Controller
{
    public function getModulos(){
        return ProductoModulo::all();
    }
    public function saveProducto(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required',
            'minutos' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors()
            ], 422);
        }
        $producto = Producto::updateOrCreate(['id'=>$request->id],$request->all());
    
    }
    public function deleteProducto(Request $request){
        $producto = Producto::find($request->id)->delete();
        return $producto;
    
    }
    public function getProducto($id){
        $producto = Producto::find($id);
        return $producto;
    }
    public function getProductos(){
        $modulos = $this->getModulos();
        $productos = Producto::all();
        $result = [];
        foreach($modulos as $modulo){
            $res_modulo = ['nombre'=>$modulo->nombre, 'items'=>[]];
            foreach($productos as $producto){
                if($producto->id_modulo == $modulo->id){
                    $res_modulo['items'][]= $producto;
                }
            }
            $result[] = $res_modulo;
        }
        return $result;
    }
}
