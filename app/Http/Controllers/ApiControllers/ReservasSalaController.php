<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReservaSala;
use App\Models\HoraSala;

class ReservasSalaController extends Controller
{
    public function getAllReservas() {
        $reservas = ReservaSala::with('horaDesde', 'horaHasta', 'user')->where('fecha', '>=', date('Y-m-d'))->orderBy('created_at', 'desc')->get();
        return response()->json($reservas, 200);
    }

    public function getDesdeHoras($fecha)
    {
        //Definir las horas de inicio de reuniones filtrando las ocupadas
        $reservasToday = ReservaSala::whereDate('fecha', $fecha)->get();
        $horasOcupadas = array();
        //armamos un array con las horas ocupadas
        foreach($reservasToday as $reserva) {
            for ($i=$reserva->desde; $i < $reserva->hasta; $i++) {
                array_push($horasOcupadas, $i);
            }
            array_push($horasOcupadas, $reserva->hasta);
        }
        //las excluimos en la consulta
        $horasLibres = HoraSala::whereNotIn('id', $horasOcupadas)->get();
        return response()->json($horasLibres, 200);
    }

    public function getHastaHoras($fecha, $desdeHora) {
        $horasOcupadas = array();
        $reservasToday = ReservaSala::whereDate('fecha', $fecha)->get();
        //definimos las horas previas a la seleccionada y la seleccionada
        $hoursBf = HoraSala::where('id', '<=', $desdeHora)->get();
        foreach($hoursBf as $hrB) {
            array_push($horasOcupadas, $hrB->id);
        }
        //abrimos un ciclo para usar las horas de reserva para tomar las horas ocupadas
        foreach($reservasToday as $reserva) {
            if($reserva->desde > $desdeHora) {
                for ($i=$reserva->desde + 1; $i < $reserva->hasta; $i++) {
                    array_push($horasOcupadas, $i);
                }
                array_push($horasOcupadas, $reserva->hasta);
            }
        }
        //hay que excluir las horas superiores al la mayor de las ocupadas siempre y cuando esta no sea igual al punto desde
        if(max($horasOcupadas) == $desdeHora) {
            $horasLibres = HoraSala::where('id', '>', $desdeHora)->get();
        }
        else {
            $horasSobrantes = HoraSala::where('id', '>', max($horasOcupadas))->get();
            foreach($horasSobrantes as $hora) {
                array_push($horasOcupadas, $hora->id);
            }
            $horasLibres = HoraSala::whereNotIn('id', $horasOcupadas)->get();
        }
        return response()->json($horasLibres, 200);
    }

    public function saveReunion(Request $request) {
        $reserva = ReservaSala::updateOrCreate(['id' => $request->id], $request->all());
        return response()->json($reserva, 201);
    }

    public function deleteReserva($reserva_id){
        $reserva = ReservaSala::find($reserva_id);
        $reserva->delete();
        return response()->json($reserva, 200);
      }
}
