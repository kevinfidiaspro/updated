<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use App\Models\EmpresaTFG;
use App\Models\TFGPotenciales;
use App\Models\TiposTFGPotenciales;

use Illuminate\Http\Request;

class PotencialTfgController extends Controller
{
    public function getEmpresas(Request $request){
        return EmpresaTFG::all();
    }
    public function savePotencial(Request $request){
        TFGPotenciales::updateOrCreate(['id'=>$request->id],$request->all());
        return $this->generatePotencialesDia($request->dia);
    }
    public function getResumenMes(Request $request){
        $potenciales = TFGPotenciales::groupBy('id_empresa','dia')->orderBy('dia');
        if($request -> inicio != null){
            $potenciales->whereDate('dia','>=',$request->inicio);
        }
        if($request -> fin != null){
            $potenciales->whereDate('dia','<=',$request->fin);
        }
        if($request -> web != null){
            $potenciales->where('id_empresa',$request->web);
        }
        $potenciales = $potenciales->selectRaw('sum(cantidad) as total, id_empresa,dia')->with('Empresa')->get();
        $result = [];
        $total = ['dia'=>'Total','total'=>0];
        foreach($potenciales as $potencial){
            if(($result[$potencial->dia]??null )== null) $result[$potencial->dia] = ['total'=>0];
            if(!isset($total[$potencial->empresa['nombre']])) {$total[$potencial->empresa['nombre']] = 0;}
            $total[$potencial->empresa['nombre']] +=$potencial->total;
            $result[$potencial->dia][$potencial->empresa['nombre']] = $potencial->total;
            $result[$potencial->dia]['total'] += $potencial->total;
            $total['total'] +=$potencial->total;
        }
        $array = $result;
        $result = array_map(function ($data, $date) {
            $data['dia'] = explode('-',$date)[2];
            return $data;
        }, $array, array_keys($array));
        $result[] = $total;
        $headers = $this->getPotencialesHeaders($request->web);
        array_splice($headers,1,1);
        return ['data'=>$result,'headers'=>$headers];
    }
    public function getPotencialesHeaders($web = null){
        $headers = [
            [ 'text'=> "Dia", 'value'=> "dia" ],
            [ 'text'=> "Tipo", 'value'=> "tipo" ],
        ];
        if($web){
            $empresas = EmpresaTFG::where('id',$web)->get();
        }
        else{
            $empresas = EmpresaTFG::all();
        }
        foreach( $empresas as $empresa){
            $headers[] = ['text'=>$empresa->nombre,'value'=>$empresa->nombre];
        }
        $headers[]= ['text'=>'Total','value'=>'total'];
        return $headers;
    }
    public function getPotencialesDia(Request $request, $dia){
        return $this->generatePotencialesDia($dia);
    }
    public function generatePotencialesDia($dia){
        $tipos = TiposTFGPotenciales::all();
        $empresas = EmpresaTFG::all();
        $result = [];
        $totales = ['dia'=>$dia,'tipo'=>'TOTAL'];
        foreach($tipos as $tipo){
            $temp= ['dia'=>$dia,'tipo'=>$tipo->nombre];
            foreach($empresas as $empresa){
                $temp[$empresa->nombre] =  TFGPotenciales::updateOrCreate(['id_empresa'=>$empresa->id,'id_tipo'=>$tipo->id,'dia'=>$dia],['id_empresa'=>$empresa->id,'id_tipo'=>$tipo->id,'dia'=>$dia]);
                if(($totales[$empresa->nombre]??null)== null ){
                    $totales[$empresa->nombre] = $temp[$empresa->nombre]->cantidad;
                }else{
                    $totales[$empresa->nombre] += $temp[$empresa->nombre]->cantidad;
                }
                
            }
            $result[] = $temp;
        }
        $result[]=$totales;
        return $result;
    }
}
