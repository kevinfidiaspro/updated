<?php

namespace App\Http\Controllers\ApiControllers;

use DB;
use PDF;
use Auth;
use Storage;
use Carbon\Carbon;
use App\Traits\Basic;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Deuda;
use App\Models\Recibo;
use App\Models\NroNota;
use App\Models\NroFactura;
use App\Models\NroPresupuesto;
use App\Models\ReciboServicio;
use App\Models\NroParteTrabajo;
use App\Models\Albaranes\AlbaranesEnviado;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReciboRequest;
use Illuminate\Support\Facades\Hash;

class ReciboController extends Controller
{
  /* tipo -> 'factura, presupuesto, nota, parte-trabajo'
     iva  -> true / false
  */

  public function getReciboById($recibo_id){
    $recibo = Recibo::with('servicios', 'cliente:id,email')->where('id', $recibo_id)->get()->first();
    return response()->json($recibo, 200);
  } 
  
  public function getReciboByName($elemento){
    $recibo = AlbaranesEnviado::with('cliente:id,email')->where('nro_factura',$elemento)->get()->first();
    return response()->json($recibo, 200);
  } 
  
  public function saveRecibo(ReciboRequest $request, $tipo, $convertir_factura){ 
    try {
      $current_pdf = null;
      $nombre_archivo = null;
      $current_recibo = Recibo::find($request->id);

      if($current_recibo)
      {
        $this->deleteFileByTipo($current_recibo, $tipo);
      }

      if($convertir_factura == 'true')
      {
        $recibo_duplicado = $current_recibo->replicate();
        $recibo = Recibo::updateOrCreate(['id' => $recibo_duplicado->id], $request->except(['servicios', 'nro_presupuesto_id']));          
        $recibo->save();
      }
      else
      {
        $recibo = Recibo::updateOrCreate(['id' => $request->id], $request->except(['servicios', 'nro_presupuesto_id']));      
      }

      $recibo->servicios()->delete();
      $servicios = $this->saveReciboServicios($recibo, $request->servicios, $request->user_id);

      /* comprobar el tipo */ // User - Cliente        

      if($tipo == 'presupuesto'){
        $nombre_archivo = $this->savePresupuesto($recibo, $tipo, $request->user_id);
        $recibo->presupuesto_url = $nombre_archivo;
      }
      if($tipo == 'factura'){
        $nombre_archivo = $this->saveFactura($recibo, $tipo, $request->user_id, $request->albaranes, $request->checkbox);
        $recibo->factura_url = $nombre_archivo;
      }
      if($tipo == 'nota'){
        $nombre_archivo = $this->saveNota($recibo, $request->user_id);
        $recibo->nota_url = $nombre_archivo;
      }
      if($tipo == 'parte-trabajo'){
        $this->asociarPresupuesto($recibo, $request->nro_presupuesto_id, $request->user_id);
      }

      /*guardar el nombre de el archivo en el registro*/
      $recibo->save();
      return response()->json($recibo->load('servicios', 'cliente:id,email'), 200);
    }
    catch (Exception $exception) {
      return response()->json('error', 301);
    }
    return response()->json('error', 500);
  }

  private function saveFactura(Recibo $recibo, $tipo, $userId, $albaranes, $checkbox){

    $nFactu = NroFactura::where('recibo_id', $recibo->id)->first();

    if ($nFactu != null) {
      // # START numero de factura
      // #  cambiado oscar 
      $factura =  NroFactura::where(['user_id' => Auth::user()->id])->get();
      $valorFactura = (1*count($factura));  
      $nroFactura=  Basic::completarConCero($valorFactura, 4);
      // # END numero de factura
    }
    else
    {
      // # START numero de factura
      // #  cambiado oscar 
      $factura =  NroFactura::where(['user_id' => Auth::user()->id])->get();
      $valorFactura = (1*count($factura) + 1*1);  
      $nroFactura=  Basic::completarConCero($valorFactura, 4);
      // # END numero de factura
    }
    $current_deuda = null;
    $nro_factura = NroFactura::updateOrCreate(['recibo_id' => $recibo->id],['user_id' => $userId,'nro_factura' => $nroFactura]);

    /*Comprobar si existe la relacion*/
    $current_deuda = $nro_factura->deuda;
    if($current_deuda){
       $current_deuda->deuda_id = $nro_factura['id'];
       $current_deuda->total = $recibo->total;
       $current_deuda->save();
    }else{
      $nro_factura->deuda()->create([
        'total' => $recibo->total,
        'user_id' => $userId,
        'nro_factura' => $nroFactura
      ]);
    }  

    // contabilizar albaranes con numero factura creada 
    foreach ($checkbox as $chbx) {
      if($chbx != null){
        $nombre = "Factura ".$nroFactura; 
        $contabilizado = DB::table('albaranes_enviados')->where(['nro_factura' => $chbx])->update(array('contabilizado'=> $nombre));
        // Prueba añadiendo columna en BD --> no funciona
        // $contabilizado = DB::table('albaranes_enviados')->where(['nro_factura' => $chbx])->update(array('contabilizado'=> $nombre, 'cobrado'=> $nombre));
      }
    }
    // Almacenamos el PDF
    $nombre_archivo = $this->savePdf_AUX($recibo->load('cliente', 'servicios', 'cliente.provincia'), $nroFactura, 'factura', $userId, $albaranes);
    return $nombre_archivo;
  }

  public function removeContabilizado($elemento,$idServicio,$idRecibo){
      // Comprobamos si es un albaran y eliminamos el contabilizado
      $elementNro = strval(substr($elemento, -4));

      $idAlb = AlbaranesEnviado::where('nro_factura' , $elementNro)->get()->first();
      $idAlbaran = $idAlb->id;

      $contabilizado = AlbaranesEnviado::findOrFail($idAlbaran);
      $contabilizado->contabilizado =  null;
      // Prueba añadiendo columna en BD --> no funciona
      // $contabilizado->cobrado = 'no contabilizado';
      $contabilizado->update();
            
      return response()->json(['status' => 200, 'contabilizado' => $contabilizado]);

      // Buscamos el Servicio y guardamos la id del recibo y la del usuario para su posterior uso
      $existeRecibo = ReciboServicio::where('id', $idServicio)->get()->first();
      if($existeRecibo){
        $reciboServicio = ReciboServicio::where('id', $idServicio)->get()->first();
        $reciboServId = $reciboServicio->recibo_id;
        $reciboUserId = $reciboServicio->user_id;
        $reciboServicio->delete();
      }

      // Si no queda ningun servicio en el listado almacenamos una linea ficticia para poder guardar la factura ### falla desde los cambios del viernes
      $vacio = ReciboServicio::where('recibo_id',$idRecibo)->get()->first();
      var_dump($vacio);

      if (!$vacio) {
        var_dump('entra vacio listado');
        $reciboServicio = ReciboServicio::create([
          'user_id' => $reciboUserId,
          'recibo_id' => $idRecibo,
          'descripcion' => 'Descripción',
          'cantidad' => 0,
          'precio' => 0,
          'importe' => 0
        ]);
        // Devolvemos el servicio creado para mostrar en la lista
        return $reciboServicio;
      }
      return null;
  }

  private function savePresupuesto(Recibo $recibo, $tipo, $userId){
    $nro_presupuesto = NroPresupuesto::updateOrCreate(
      ['recibo_id' => $recibo->id], 
      ['user_id' => $userId], 
      ['recibo_id' => $recibo->id]      
    );
    $nombre_archivo = $this->savePdf($recibo->load('cliente', 'servicios', 'cliente.provincia'), $nro_presupuesto['nro_presupuesto'], 'presupuesto', $userId);
    return $nombre_archivo;
  }
  private function saveNota(Recibo $recibo, $userId){
    $current_deuda = null;
    // # START asigna numero de nota correlativo segun usuario id
    // #  cambiado oscar para numeracion correcta
    // nota
    $nota =  NroNota::where(['user_id' => Auth::user()->id])->get();
    $valorNota = (1*count($nota) + 1*1);  
    $nroNota=  Basic::completarConCero($valorNota, 4);
    // # END asigna numero de nota correlativo segun usuario id

    $nro_nota = NroNota::updateOrCreate(['recibo_id' => $recibo->id], ['user_id' => $userId], ['recibo_id' => $recibo->id]);
    $current_deuda = $nro_nota->deuda;
    /*Comprobar si existe la relacion*/
    if($current_deuda){
       $current_deuda->deuda_id = $nro_nota['id'];
       $current_deuda->total = $recibo->total;
       $current_deuda->save();
    }else{      
      $nro_nota->deuda()->create(
        [
        'total' => $recibo->total,
        'user_id' => $userId,
        'nro_nota' => $nroNota
        ]
      );
    }
    $nombre_archivo = $this->savePdf($recibo->load('cliente', 'servicios', 'cliente.provincia'), $nroNota, 'nota', $userId);
    return $nombre_archivo;
  }
  private function asociarPresupuesto(Recibo $recibo, $nro_presupuesto_id, $userId){
    // # START asigna numero de parte-trabajo correlativo segun usuario id
    // #  cambiado oscar para numeracion correcta
    // partetrabajo
    $partetrabajo =  NroParteTrabajo::where(['user_id' => Auth::user()->id])->get();
    $valorPartetrabajo = (1*count($partetrabajo) + 1*1);  
    $nroPartetrabajo=  Basic::completarConCero($valorPartetrabajo, 4);
    // # END asigna numero de parte-trabajo correlativo segun usuario id

    $saved_nro_parte_trabajo = NroParteTrabajo::where('recibo_id', $recibo->id)->get()->first();
    if($saved_nro_parte_trabajo){
       $saved_nro_parte_trabajo->delete();
    }
    return $nro_parte_trabajo = NroParteTrabajo::updateOrCreate(
      [
      'recibo_id' => $recibo->id,
      'user_id' => $userId,
      'nro_presupuesto_id' => $nro_presupuesto_id,
      'nro_parte_trabajo' => $nroPartetrabajo
      ]
    );
  }
  private function deleteFileIfExists($nombre_disco, $nombre_archivo){
    if(Storage::disk($nombre_disco)->exists($nombre_archivo)){
       Storage::disk($nombre_disco)->delete($nombre_archivo);
    }
  }
  private function saveReciboServicios(Recibo $recibo, $servicios, $userId){

    foreach ($servicios as $key => $servicio) {
      $recibo->servicios()->create([
        'user_id' => $userId,
        'descripcion' => $servicio['descripcion'],
        'cantidad' => $servicio['cantidad'],
        'precio' => $servicio['precio'],
        'importe' => $servicio['importe']
      ]);
    }
    return $recibo->servicios;
  }
  public function savePdf(Recibo $recibo, $nro_factura, $tipo, $userId){
    $view = $tipo == 'nota' ? 'pdf.nota' : 'pdf.recibo';
    $user = User::where('id',Auth::id())->with('provincia')->first();
    $pdf = PDF::loadView($view, [
      'recibo'      => $recibo,
      'userLog'      => $user,
      'nro_factura' => $nro_factura,
      'tipo'        => $tipo
    ]);
    $nro = str_pad($nro_factura, 4, '0', STR_PAD_LEFT);
    $nombre_archivo = strtoupper($tipo) . '_' . $nro . '.pdf';
    Storage::disk('recibos')->put($nombre_archivo, $pdf->output());
    $file_path = Storage::disk('recibos')->path($nombre_archivo);    
    //Determinar archivospara gestor documental
    $archivosGestorDocumental = $this->saveFilesForDocumentsApp($nombre_archivo, $recibo, $nro_factura, $tipo, $userId,$pdf->output());  
    return $nombre_archivo;
  }
  public function savePdf_AUX(Recibo $recibo, $nro_factura, $tipo, $userId, $albaranes){
     $view = $tipo == 'nota' ? 'pdf.nota' : 'pdf.recibo';
    $user = User::where('id',Auth::id())->with('provincia')->first();
    $totalImporte = 0;
    foreach($albaranes as $albaran){
        $totalImporte = 1*($totalImporte + $albaran['importe']);
    }
    $pdf = PDF::loadView($view, [
      'recibo'      => $recibo,
      'userLog'      => $user,
      'nro_factura' => $nro_factura,
      'tipo'        => $tipo,
      'albaranes' => $albaranes,
      'totalImporteAlbaran' =>  $totalImporte
    ]);
    $nro = str_pad($nro_factura, 4, '0', STR_PAD_LEFT);
    $nombre_archivo = strtoupper($tipo) . '_' . $nro . '.pdf';
    Storage::disk('recibos')->put($nombre_archivo, $pdf->output());
    $file_path = Storage::disk('recibos')->path($nombre_archivo);    
    //Determinar archivospara gestor documental
    $archivosGestorDocumental = $this->saveFilesForDocumentsApp($nombre_archivo, $recibo, $nro_factura, $tipo, $userId,$pdf->output());   
    return $nombre_archivo;
  }
  public function saveFilesForDocumentsApp($nombre_archivo, $recibo, $nro_factura, $tipo, $userId, $pdf){
    #Salvar archivos en los directorios correspondientes al usuario
    $path = 'userId_' .$userId;
    // Storage::makeDirectory($path);    
    $putDocument =  Storage::disk('documentos')->put($path.'/'. $tipo . '/' .$nombre_archivo, $pdf);    
  }
  private function deleteFileByTipo(Recibo $current_recibo, $tipo){
    $nombre_pdf = null;

    if($tipo == 'presupuesto'){
       $nombre_pdf = $current_recibo->presupuesto_url;
    }

    if($tipo == 'factura'){
       $nombre_pdf = $current_recibo->factura_url;
    }

    if($tipo == 'nota'){
       $nombre_pdf = $current_recibo->nota_url;
    }

    return $this->deleteFileIfExists('recibos', $nombre_pdf);
  }
}
