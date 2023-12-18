<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Proyecto;
use App\Models\VentasDiarias;
use App\Models\User;
class MarketingResource extends JsonResource
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
        $fecha = explode('-',$this->mes);
        $month = $months[intval($fecha[1])-1];
        $id_lead_form = $this->id_web;
        $ventas = VentasDiarias::whereMonth('dia',$fecha[1])->whereYear('dia',$fecha[0])->whereHas('Factura' , function($query) use($id_lead_form){
            $query->whereHas('cliente' , function($query)use($id_lead_form) {     
                $query->whereHas('Proyectos' , function($query) use($id_lead_form) {
                    $query->where('id_lead_form', $id_lead_form);
                });     
            });
        })->join('facturas','facturas.id','ventas_diarias.id_factura');
        $this->potenciales = Proyecto::whereMonth('fecha_alta',$fecha[1])->whereYear('fecha_alta',$fecha[0])->where('estado_id', '=', 3)->count();
        $this->cerrados = $ventas->count();
        $this->ventas  = $ventas->sum('facturas.total');
        return [
            'id'=>$this->id,
            'mes_str'=>$month,
            'web_str'=>isset($this->Web->name)?$this->Web->name:'',
            'mes'=>$this->mes,
            'invertido'=>$this->invertido,
            'clics'=>$this->clics,
            'id_web'=>$this->id_web,
            'potenciales'=> $this->potenciales,
            'cerrados'=> $this->cerrados,
            'ventas'=> $this->ventas,
            'inv_pot'=>$this->potenciales != 0 ?number_format($this->invertido/$this->potenciales,2):0,
            'venta_inv'=>$this->invertido != 0 ?number_format($this->ventas/$this->invertido,2):0,
            'pres_visitas'=>($this->clics != 0?number_format($this->potenciales/$this->clics*100,2):0),
            'per_ventas'=>$this->potenciales!=0?number_format($this->cerrados/$this->potenciales*100,2):0,
            'prec_medio'=>$this->cerrados!=0?number_format($this->ventas/$this->cerrados,2):0,
        ];
    }
}
