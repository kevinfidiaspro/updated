<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proyecto;
use GuzzleHttp\Handler\Proxy;
use DB;

class EstadisticasApiController extends Controller
{
    public function getProyectosData($desde, $hasta) {
        //consultamos los proyectos en el intervalo de tiempo pedido
        $proyectos = Proyecto::whereBetween('fecha_alta', [$desde, $hasta])->get();
        //tomamos el valor numerico de los meses
        $mesdesde = date('m', strtotime($desde));
        $meshasta = date('m', strtotime($hasta));
        //declaramos los arreglos para los totales que necesitaremos
        $total_proyectos[] = [];
        $total_gastos[] = [];
        $total_ingresos[] = [];
        //definimos el volumen de los arreglos en base al valor numerico de los meses
        for ($i = $mesdesde-1; $i < $meshasta; $i++) {
            $total_proyectos[$i] = 0;
            $total_gastos[$i] = 0;
            $total_ingresos[$i] = 0;
        }
        //creamos un ciclo para rellenar los totales de cada uno por mes
        foreach($proyectos as $key => $proyecto) {
            $mes = date("m", strtotime($proyecto->fecha_alta));
            $total_proyectos[$mes-1] = $total_proyectos[$mes-1] + 1;
            $total_ingresos[$mes-1] = $total_ingresos[$mes-1] + $proyecto->pvp;
            $total_gastos[$mes-1] = $total_gastos[$mes-1] + $proyecto->pvp_gasto;
        }
        //incluimos todo en un nuevo arreglo, junto a los nombres de los meses
        for ($i = $mesdesde-1; $i < $meshasta; $i++) {
            $labels = [
                'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',
                'Agosto','Septiembre','Octubre','Noviembre','Diciembre'
            ];
            $estadisticaTotales[$i] = [
                'Mes' => $labels[$i],
                'ProyectosTotales' => $total_proyectos[$i],
                'IngresosTotales' => $total_ingresos[$i],
                'GastosTotales' => $total_gastos[$i],
                'Totaltodo' => $total_ingresos[$i] - $total_gastos[$i],
            ];
        }
        return $estadisticaTotales;
    }

    public function getdetalleData($desde, $hasta, $servicio_id) {
        $find = Proyecto::with('servicio')
                    ->where('fecha_alta', '>=', $desde)
                    ->where('fecha_alta', '<=', $hasta);
        $find->when($servicio_id != 'null', function ($query) use ($servicio_id) {
            return $query->where('servicio_id', $servicio_id);
        });
        $proyectos = $find->get();

        return $proyectos;
    }

}
