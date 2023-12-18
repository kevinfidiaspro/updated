<?php

namespace App\Http\Controllers\ApiControllers;

use File;
use Storage;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade as PDF;

use App\Traits\Basic;
use App\Traits\Files\HandlerFiles;

use App\Http\Requests\AlbaranRequest;

use App\Http\Resources\AlbaranResource;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Recibo;
use App\Models\NroNota;
use App\Models\Cliente;
use App\Models\Albaran;
use App\Models\Proveedor;
use App\Models\NroFactura;
use App\Models\ReciboServicio;
use App\Models\AlbaranEnviadoItemAgregado;
use App\Models\Albaranes\AlbaranesEnviado;

class AlbaranController extends Controller
{

  public function getAlbaranes($user_id){
    $albaranes = AlbaranResource::collection(Albaran::orderBy('created_at', 'DESC')->where('user_id', '=', $user_id)->get());
    $proveedores = Proveedor::where('user_id', '=', $user_id)->orderBy('created_at', 'DESC')->get();
    return response()->json(['status' => 200,'albaranes' => $albaranes,'proveedores' => $proveedores]);
  }

  public function getAlbaranById($albaran_id){
    $albaran = new AlbaranResource(Albaran::where('id', $albaran_id)->get()->first());
    return response()->json(['status' => 200,'albaran' => $albaran]);
  }

  protected function pathServer(){
      $PATH = $_SERVER['DOCUMENT_ROOT'];
      $pathPublicOut = explode('public',$PATH);
      $res = $pathPublicOut[0]; 
      return $res;
  }

  public function saveAlbaran(Request $request){    
    $albaran = new Albaran;
    $albaran->user_id = $request->user_id;
    $albaran->proveedor_id = $request->proveedor_id;
    $albaran->descripcion = $request->descripcion;
    $albaran->fecha = $request->fecha;
    $albaran->save();
    // $destination  = $this->pathServer() . "/storage/app/public/albaranes/recibidos/";
    // $storeFiles = HandlerFiles::store($request, $destination);
    // $storeFiles->original['nombresArchivos'];
    $destination  = $this->pathServer() . "/storage/app/public/documentos/userId_". $request->user_id . "/factura_recibidas/";
    $storeFiles = HandlerFiles::store($request, $destination);
    $storeFiles->original['nombresArchivos'];
    if (count($storeFiles->original['nombresArchivos']) > 0) {
      $n = Albaran::findOrFail($albaran->id);
      $n->pdf = $storeFiles->original['nombresArchivos'];
      $n->update();
    }
    return response()->json($albaran, 200);
  }

  public function updatelbaran(AlbaranRequest $request, $id){
    $albaran = Albaran::findOrFail($id);
    $albaran->user_id = $request->user_id;
    $albaran->proveedor_id = $request->proveedor_id;
    $albaran->descripcion = $request->descripcion;
    $albaran->fecha = $request->fecha;
    $albaran->update();
    // $destination  = $this->pathServer() . "storage/app/public/albaranes/recibidos/";
    // $storeFiles = HandlerFiles::store($request, $destination);
    // $storeFiles->original['nombresArchivos'];
    $destination  = $this->pathServer() . "/storage/app/public/documentos/userId_". $request->user_id . "/factura_recibidas/";
    $storeFiles = HandlerFiles::store($request, $destination);
    $storeFiles->original['nombresArchivos'];     
    if (count($storeFiles->original['nombresArchivos']) > 0) {
      $n = Albaran::findOrFail($albaran->id);
      $n->pdf = json_encode($storeFiles->original['nombresArchivos']);
      $n->update();
    }
    return response()->json($albaran, 200);
  }

  public function pdfAPARTE($request){
       return  Storage::disk('albaranes')->put('xxx', $request->imagen);
  }  

  public function deleteAlbaran($albaran_id){
    $albaran = Albaran::find($albaran_id);
    if(Storage::disk('albaranes')->exists($albaran->imagen)){
       Storage::disk('albaranes')->delete($albaran->imagen);
    }
    $albaran->delete();
    return response()->json('Albaran eliminado', 200);
  }

  public function getnviados($user_id){
    $enviados = AlbaranesEnviado::with('cliente')->orderBy('created_at', 'DESC')->where('user_id', '=', $user_id)->get();     
    return response()->json(['status' => 200,'enviados' => $enviados]);
  }

  public function albaranesEnviadosF(Request $request){ 
    $data =  json_decode($request->enviados);
    $total = 0;
    $cantidad = 0;
    foreach ($data as $value) {
      $total = (1*$total + 1*$value->importe);
      $cantidad = $cantidad + $value->cantidad;
    }
    // # START asigna numero de albaran correlativo segun usuario id
    // #  cambiado oscar para numeracion correcta de albaranes
    // $albaranes =  AlbaranesEnviado::get();  // ANTIGUA LINEA
    $albaranes =  AlbaranesEnviado::where(['user_id' => Auth::user()->id])->get();
    $valor = (1*count($albaranes) + 1*1);  
    $nroAl=  Basic::completarConCero($valor, 4);
    // # END asigna numero de albaran correlativo segun usuario id
    $aE = new AlbaranesEnviado;
    $aE->user_id =  Auth::id();
    $aE->cliente_id =  $data[0]->cliente->id;
    // $aE->precio =  $total;
    $aE->importe =  $total;
    $aE->fecha =  $request->fecha_emision;
    $aE->cantidad =  $cantidad;
    $aE->nro_factura = $nroAl;
    $aE->url =  'ALBARAN_' . $nroAl . '.pdf';
    $aE->save();
    foreach ($data as $value) {
        //tabla relacional
        $aeis =new AlbaranEnviadoItemAgregado;
        $aeis->albaran_enviado_id = $aE->id;
        $aeis->descripcion = $value->descripcion;
        $aeis->cantidad = $value->cantidad;
        $aeis->precio = $value->precio;
        $aeis->importe = $value->importe;
        $aeis->save();
    }
    $data = [
      'albaran' => $aE,
      'data' =>  $data,
      'userLog' => Auth::user(),
      'total' => $total,
      'nro_factura' =>  $nroAl,
      'fecha_emision' => $request->fecha_emision,
      'cliente' =>$data[0]->cliente
    ];
    $pdf = PDF::loadView('pdf.albaranEnviado', $data)->save(storage_path('app/public/albaranes/enviados/') . $aE->url);
    return  $data;    
  }

  public function deleteAlbaranEnviado($albaran_id){
    $albaran = AlbaranesEnviado::find($albaran_id);
    $albaran->delete();
    return response()->json('Albaran eliminado', 200);
  }

  public function generarFactura(Request $request){   
    $recibo = null;
    $nroFact = null;
    try {
      $Countfacturas = (1*count(NroFactura::get()) + 1*1);
      $strNr = Basic::completarConCero($Countfacturas, 4);
      $nroFactura = $strNr;
      
      $recibo = new Recibo;
      $recibo->user_id = $request->user_id;
      $recibo->cliente_id = $request->cliente_id;
      $recibo->fecha = $request->fecha;
      $recibo->sub_total = $request->sub_total;
      $recibo->iva = $request->iva;
      $recibo->porcentaje_descuento = $request->porcentaje_descuento;
      $recibo->total_descuento = $request->total_descuento;
      $recibo->total  = $request->total;
      $recibo->has_iva = $request->has_iva;
      $recibo->factura_url = 'FACTURA_' . $nroFactura. '.pdf';
      $recibo->save();

      foreach($request->servicios as $servicio){
        $recioServicio = new ReciboServicio;
        $recioServicio->user_id = $request->user_id;
        $recioServicio->recibo_id = $recibo->id;

        if( $servicio['descripcion'] == null){
          ;
        }else{
          $recioServicio->descripcion = $servicio['descripcion'];
        }        
        $recioServicio->cantidad = $servicio['cantidad'];
        $recioServicio->precio = $servicio['precio'];
        $recioServicio->importe = $servicio['importe'];
        $recioServicio->save();
      }

      foreach($request->servicios as $servicio){
        $recibo = Recibo::where('id', $recibo->id)->with('servicios')->first();
        $recibo['cliente'] = Cliente::where('id', $request->cliente_id)->first();
        break;
      }

      $saveJsonAlbaran = AlbaranesEnviado::findOrFail(1*$request->albaran_id);
      $saveJsonAlbaran->json_recibo = json_encode($recibo);
      $saveJsonAlbaran->contabilizado  = 'FAC'.$nroFactura;
      $saveJsonAlbaran->update();

      $userLoged = User::where('id',$request->user_id)->first();

      $nroFact = new NroFactura;
      $nroFact->recibo_id = $recibo->id;
      $nroFact->nro_factura = $nroFactura;
      $nroFact->user_id = $userLoged->id;
      $nroFact->save();

      $data = ['nro_factura' => $nroFactura,'tipo' => 'factura','recibo' => $recibo,'userLog' => $userLoged];
      $pdf = PDF::loadView('pdf.recibo', $data)->save(storage_path('app/public/recibos/') . $recibo->factura_url);
      $pdf = PDF::loadView('pdf.recibo', $data)->save(storage_path('app/public/documentos/userId_'. $request->user_id . '/factura/') . $recibo->factura_url);

      return  response()->json(['data' => $data,'factura_generada' => true]);
    } 
    catch (Exception $e) {
        $recibo->delete();
        $nroFact->delete();
    }
  }

  public function generarNota(Request $request){
    $Countfacturas = (1*count(NroNota::get()) + 1*1);
    $strNr = Basic::completarConCero($Countfacturas, 4);
    $nroNota =  $strNr;

    $recibo = new Recibo;
    $recibo->user_id = $request->user_id;
    $recibo->cliente_id = $request->cliente_id;
    $recibo->fecha = $request->fecha;
    $recibo->sub_total = $request->sub_total;
    $recibo->iva = $request->iva;
    $recibo->porcentaje_descuento = $request->porcentaje_descuento;
    $recibo->total_descuento = $request->total_descuento;
    $recibo->total  = $request->total;
    $recibo->has_iva = $request->has_iva;
    $recibo->nota_url = 'NOTA_'. $nroNota. '.pdf';
    $recibo->save();

    foreach($request->servicios as $servicio){
      $recioServicio = new ReciboServicio;
      $recioServicio->user_id = $request->user_id;
      $recioServicio->recibo_id = $recibo->id;
      $recioServicio->descripcion = $servicio['descripcion'];
      $recioServicio->cantidad = $servicio['cantidad'];
      $recioServicio->precio = $servicio['precio'];
      $recioServicio->importe = $servicio['importe'];
      $recioServicio->save();
    }

    $saveJsonAlbaran = AlbaranesEnviado::findOrFail(1*$request->albaran_id);
    $saveJsonAlbaran->json_recibo = json_encode($recibo);
    $saveJsonAlbaran->contabilizado  = 'NOT'.$nroNota;
    $saveJsonAlbaran->update();

    $userLoged = User::where('id',$request->user_id)->first();

    $newNota = new NroNota;
    $newNota->recibo_id = $recibo->id;
    $newNota->nro_nota = $nroNota;
    $newNota->user_id = $userLoged->id;
    $newNota->save();

    $data = ['nro_factura' => $nroNota,'tipo' => 'nota','recibo' => $recibo,'userLog' => $userLoged];
    $pdf = PDF::loadView('pdf.nota', $data)->save(storage_path('app/public/recibos/') . $recibo->nota_url);    
    return  response()->json(['data' => $data]);     
  }

  public function albaranEnvidoShow (Request $request, $idAlbaran){
      $urlNota = null;
      $albaranEnviado = AlbaranesEnviado::with('itemAlbaran', 'cliente')->where('id', $idAlbaran)->first();
      // if($request->existente == true){
      //   if($request->cadena == 'NOT'){
      //       $urlNota = Nota::where('', )->first('');
      //   }
      // }
      return response()->json(['status' => 200,'albaranEnviado' => $albaranEnviado]);
  }

  public function updatealbaranesEnviadosF(Request $request, $idAlbaran){
    $data =  json_decode($request->enviados);
    $total = 0;
    $cantidad = 0;

    foreach ($data as $value) {
      $total = (1*$total + 1*$value->importe);
      $cantidad = $cantidad + $value->cantidad;
    }
    
    $aE =  AlbaranesEnviado::findOrFail($idAlbaran);
    $aE->user_id =  Auth::id();
    $aE->cliente_id =  $request->cliente_id;
    // $aE->precio =  $total;
    $aE->importe =  $total;
    $aE->fecha =  $request->fecha_emision;
    $aE->cantidad =  $cantidad;
    $aE->update();

    $clienteGet = Cliente::where('id', $request->cliente_id)->first();

    $data = [
      'albaran' => $aE,
      'data' =>  $data,
      'userLog' => Auth::user(),
      'total' => $total,
      'nro_factura' =>  $aE->nro_factura,
      'fecha_emision' => $request->fecha_emision,
      'cliente' =>$clienteGet,
    ];

    $pdf = PDF::loadView('pdf.albaranEnviado', $data)->save(storage_path('app/public/albaranes/enviados/') . $aE->url);
    return  $data;
  }
}
