<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Resources\FichajeResource;
use App\Http\Resources\FichajePdfResource;

use App\Http\Resources\FichajeCreateResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fichar;
use App\Traits\Basic;
use App\Models\User;
use App\Models\Empresa;
use Carbon\Carbon;
use Storage;
use PDF;
use DB;
use Illuminate\Support\Str;
use App\Http\Resources\VacacionResource;
class FicharController extends Controller
{
  public function ficharEsp(Request $request){
    $user = User::where('token_fichaje',$request->token)->first();
    if($user == null ) return;
    
    $ultimo_fichaje = Fichar::where('usuario_id', $user->id)
    ->whereDate('fecha', Carbon::now())
    ->get()
    ->last();

    if(!$ultimo_fichaje){
      $fichar = Fichar::create([
        'usuario_id' => $user->id,
        'fecha'      => Carbon::now()->format('Y-m-d H:i:s')
      ]);
      return response()->json('Gracias', 200);
    }

    $diferencia = Carbon::parse($ultimo_fichaje->fecha)
    ->diffInMinutes(Carbon::now());

    if($diferencia < 5){
      return response()->json(['mensaje' => 'comprobar fichaje'], 301);
    }
    $fichar = Fichar::create([
      'usuario_id' => $user->id,
      'fecha'      => Carbon::now()->format('Y-m-d H:i:s')
    ]);
 
   /* $client = new \GuzzleHttp\Client();
    ///https://app.vbservicios.es/api/saveclientes-facebook
    $response = $client->request('GET', 'https://app.yotico.es/api/random-frase');  */

    return response()->json('Gracias', 200);
  }

  public function generateToken(){
    $user_sinToken = User::where(function($query) {
      $query->where('role', 1)->orWhere('role', 3)->orWhere('role', 5);
    })->where('token_fichaje',null)->get();
    foreach($user_sinToken as $user){
      $user->token_fichaje = Str::random(11);
      $user->save();
    }

  }
  public function getEmpleadosToken(Request $request){
    $this->generateToken();
    $user = User::where('role', 1)->orWhere('role', 3)->orWhere('role', 5)->orderBy('created_at', 'DESC')->paginate(15);
    return $user;
  }
  public function getFichaje($id){
    return new FichajeCreateResource(Fichar::find($id));
  }
  public function deleteFichaje($id){
    Fichar::find($id)->delete();
  }
  public function saveFichaje(Request $request){
    Fichar::updateOrCreate(['id'=>$request->id],[
      'usuario_id'=>$request->usuario_id,
      'fecha'=>$request->fecha.' '.$request->hora,
    ]);
  }
    public function fichar($usuario_id){
      $fichar = Fichar::create([
        'usuario_id' => $usuario_id,
        'fecha'      => Carbon::now()->format('Y-m-d H:i:s')
      ]);
      return response()->json('enviado con exito', 200);
    }

    public function comprobarFichaje(Request $request, $status = null){
      $usuario_id = $request->user()->id;

      if($status){
        return $this->fichar($usuario_id);
      }

      $ultimo_fichaje = Fichar::where('usuario_id', $usuario_id)
      ->whereDate('fecha', Carbon::now())
      ->get()
      ->last();

      if(!$ultimo_fichaje){
         return $this->fichar($usuario_id);
      }

      $diferencia = Carbon::parse($ultimo_fichaje->fecha)
      ->diffInMinutes(Carbon::now());

      if($diferencia < 10){
        return response()->json(['mensaje' => 'comprobar fichaje'], 301);
      }

      return $this->fichar($usuario_id);
    }

    public function getFichajes(Request $request){
        $fichajes = $this->getFichajesByDate($request)->get();
        return response()->json(FichajeResource::collection($fichajes), 200);
    }

    public function getFichajesByUser(Request $request, $id){
      $fichajes = Fichar::with('usuario')->where('usuario_id', $id)
          ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin])
          ->get();
      return response()->json(FichajeResource::collection($fichajes), 200);
    }
    public function crearPdfFichajesTest(Request $request){
      $fecha_inicio = $request->fecha_inicio;
      $fecha_fin = $request->fecha_fin;
      $query = User::with([
      'Vacaciones'=>function($query) use ($fecha_inicio, $fecha_fin){
        $query->whereDate('fecha', '>=',$fecha_inicio)->whereDate('fecha','<=',$fecha_fin);
      },
      'fichaje'=>function($query) use ($fecha_inicio, $fecha_fin){
        $query->whereDate('fecha', '>=',$fecha_inicio)->whereDate('fecha','<=',$fecha_fin);
      }])->whereHas('fichaje',function($query) use ($fecha_inicio, $fecha_fin){
        $query->whereDate('fecha', '>=',$fecha_inicio)->whereDate('fecha','<=',$fecha_fin);
      });
      if($request->usuario){
        $query = $query->where('id',$request->usuario);
      }
      $data =  $query->get();
      $usuarios = $data->map(function ($item, $key){
        $body = collect(FichajeResource::collection($item->fichaje))->groupBy('fecha');
        $vacaciones = collect(VacacionResource::collection($item->Vacaciones))->groupBy('fecha');
        $contador_horas = $body->map(function ($x, $key){
            return count($x);
         })->max();
        $rango_horas = range(1, $contador_horas);
        $headers = collect($rango_horas)->map(function ($i){
            return "Fichaje {$i}";
          });
        return [
            'headers'      => $headers,
            'body'         => $body,
            'rango_horas'  => $rango_horas,
            'vacaciones'   => $vacaciones,
            'usuario' => $item,

        ];
        
      });
  
      $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
      
      $mes = $meses[intval(date('m',strtotime($fecha_inicio) ))-1];
      $pdf = PDF::setPaper('A4', 'portrait');
      $url = 'Fichajes '.$mes.' '.$data->first()['nombre'];
      $empresa = Empresa::find($request->id_empresa);

      $start_date = Carbon::parse($fecha_inicio);
      $end_date = Carbon::parse($fecha_fin);
  
      // Get list of dates between start and end date
      
      for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
          $dates[] = $date->format('d-m-Y');
      }

      $data = $pdf->loadView('pdf.fichajes', compact('usuarios','fecha_inicio','dates','empresa'))->output();

      Storage::disk('fichajes')->put($url.'.pdf', $data);

      return response()->json([
        'usuarios' => $usuarios,
        'url_name' => $url.'.pdf'
      ], 200);;
      //return $usuarios_res;
    }
    public function crearPdfFichajes(Request $request){

      
      $fichajes = $this->getFichajesByDate($request)->get();
      $fecha_inicio = $request->fecha_inicio;
      $fecha_fin = $request->fecha_fin;

      $items = FichajeResource::collection($fichajes)->toArray(request());
  
      $agrupar_por_usuario = collect($items)->groupBy('usuario_id');

      $usuarios = $agrupar_por_usuario->map(function ($item, $key) {
          $usuario = User::find($key);
          $horas_total = 0;
          $body = collect($item)->groupBy('fecha_fichaje');
          $horas = [];
   
          foreach($body as $key=> $elemento){
            if(!isset($horas[$key]) ) $horas[$key ] = 0;
            if(count($elemento)%2 != 0){
              $horas[$key ] = 'Error';
            }else{
              for($i = 0 ; ($i+1)<= count($elemento) ; $i +=2){
                $tiempo = round(Carbon::parse($key.' '.$elemento[$i]['hora'])->diffInSeconds(Carbon::parse($key.' '.$elemento[$i+1]['hora']))/3600,2);
                $horas[$key] += $tiempo;
                $horas_total+=$tiempo;
              }
            }
            
          }
          $vacaciones = collect(VacacionResource::collection($usuario->Vacaciones))->groupBy('fecha');
          $contador_horas = collect($body)->map(function ($x, $key){
                return count($x);
        })->max();

          $rango_horas = range(1, $contador_horas);

          $headers = collect($rango_horas)->map(function ($i){
            return "Fichaje {$i}";
          });

          return [
            'headers'      => $headers,
            'body'         => $body,
            'rango_horas'  => $rango_horas,
            'usuario'      => $usuario,
            'vacaciones'   => $vacaciones,
            'horas' =>$horas,
            'item'=>collect($item)->groupBy('fecha_fichaje'),
            'horas_total'=>$horas_total,
          ];
      });

      //return $usuarios;
      //return $usuarios;
      $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
      $año = date('Y',strtotime($fecha_inicio) );
      $mes = $meses[intval(date('m',strtotime($fecha_inicio) ))-1];
      $pdf = PDF::setPaper('A4', 'portrait');
      $url = 'Fichajes '.$mes.' '.$usuarios->first()['usuario']['nombre'];
      $empresa = Empresa::find($request->id_empresa);

      $start_date = Carbon::parse($fecha_inicio);
      $end_date = Carbon::parse($fecha_fin);
  
      // Get list of dates between start and end date
      
      for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
          $dates[] = $date->format('d-m-Y');
      }

      $data = $pdf->loadView('pdf.fichajes', compact('usuarios','fecha_inicio','dates','empresa'))->output();

      Storage::disk('fichajes')->put($url.'.pdf', $data);

      return response()->json([
        'usuarios' => $usuarios,
        'url_name' => $url.'.pdf'
      ], 200);;
    }

    private function getFichajesByDate(Request $request){
      $fechaInicio = $request->fecha_inicio;

      $fechaFin = $request->fecha_fin;

      return $request->whenFilled('usuario', function ($input) use ($fechaInicio, $fechaFin, $request){
          return Fichar::orderBy('fecha', 'ASC')
            ->with('usuario')
            ->where('usuario_id', $request->usuario)
            ->whereDate('fecha', '>=',$fechaInicio)->whereDate('fecha','<=',$fechaFin);
      }, function () use ($fechaInicio, $fechaFin){
          return Fichar::orderBy('fecha', 'ASC')
            ->with('usuario')
            ->whereDate('fecha', '>=',$fechaInicio)->whereDate('fecha','<=',$fechaFin);
      });
    }
}
