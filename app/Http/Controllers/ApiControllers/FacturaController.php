<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Resources\FacturaResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\NroFactura;
use App\Models\AnioFiscal;
use App\Models\Cuenta;
use App\Models\Recibo;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use Storage;
use App\Models\Proyecto;
use App\Models\Albaranes\AlbaranesEnviado;
use App\Models\Factura;
use App\Models\ItemsFactura;
use PDF;
use Validator;
use App\Mail\FacturasMensualesMail;
class FacturaController extends Controller
{
  public function DuplicateFactura($id,$tipo){
    $factura_old = Factura::find($id);
    if($factura_old != null){
      $anio = AnioFiscal::latest()->first();
      $factura_count = Factura::where('tipo',$tipo)->where('id_anio',$anio->id)->latest('nro_factura')->first();
     
    
      if ($factura_count){
        if(strval($factura_count->anio)== strval($anio->year)){
          $nro_factura = $factura_count['nro_factura'] + 1;
        }
        else{
          $nro_factura= 1;
        }
      }else{
        $nro_factura = 1;
      }
      
      $factura = Factura::create([
        'id_proyecto'=>$factura_old-> id_proyecto,
        'id_cliente'=>$factura_old-> id_cliente,
        'nro_factura'=>$nro_factura,
        'id_anio'=>$anio->id,
        'file_name'=>$factura_old->file_name,
        'url'=>$factura_old->url,
        'total'=>$factura_old->total,
        'descuento'=>$factura_old->descuento,
        'fecha'=>date('Y-m-d'),
        'status_iva' =>$factura_old->status_iva,
        'id_cuenta' =>$factura_old->id_cuenta,
        'tipo'=>$tipo,
        'notas'=>$factura_old->notas
      ]);

     
      $factura->items_factura()->createMany($factura_old->items_factura->toArray());
      $factura_no_change = Factura::find($factura->id);

      $data_report = $this->generateReports($tipo == 1?'factura':'factura-proforma', $factura_no_change);

      $factura->url = $data_report['url'];
      $factura->file_name = $data_report['path'];
      $factura->id_anio = $anio->id;
      
      $factura->update();
    

      return $factura;
    }else{
      return response("Factura no encontrada",404);
    }
  }
  public function CambiarAnioFiscal(){
    $anio = AnioFiscal::latest()->first();
    $nuevo = AnioFiscal::create(['year'=>($anio->year+1)]);
    
  }
  public function getFacturasByProyecto($id_proyecto){
    $facturas = Factura::with('items_factura','proyecto.usuario')->where('id_proyecto',$id_proyecto)->orderBy('fecha', 'desc')->get();
    return response()->json($facturas, 200);
  }
  public function getFacturasByCliente($id_cliente){
    $facturas = Factura::with('items_factura','proyecto.usuario')->where('id_cliente',$id_cliente)->orWhereHas('proyecto',function($query) use($id_cliente){
      $query->where('usuario_id',$id_cliente);
    })->orderBy('fecha', 'desc')->get();
    return response()->json($facturas, 200);
  }
  public function getFacturas($user_id){
    $facturas = Recibo::whereHas('nro_factura')->with(['ingresos','nro_factura' => function($query){
        $query->orderBy('nro_factura', 'ASC');
    }])->where('user_id', '=', $user_id)->orderBy('id', 'DESC')->get();

    $facturas_resource = FacturaResource::collection($facturas);
    return response()->json($facturas_resource, 200);
  }
  public function deleteFacturas($id){
    $factura = Factura::find($id)->delete();
    $items = ItemsFactura::where('id_factura',$id)->delete();
  }
  public function deleteFactura($factura_id){
    $factura = Recibo::find($factura_id);

    $nro_factura = NroFactura::get(['id'])->last();

    if($factura->nro_factura['id'] == $nro_factura->id){

      if(Storage::disk('recibos')->exists($factura->factura_url)){
         Storage::disk('recibos')->delete($factura->factura_url);
      }

      $factura->nro_factura()->delete();

      if($factura->nro_presupuesto()->exists()){
         $factura->factura_url = null;
         $factura->save();
         return response()->json('factura eliminada', 200);
      }

      $factura->servicios()->delete();
      $factura->delete();
      return response()->json('factura eliminada', 200);
    }

    return response()->json('no se puede eliminar', 301);
  }
  public function getDataAlbaranes(Request $request, $user_id){

    $albaranesEnviados = AlbaranesEnviado::with('cliente')
                                          ->where('user_id', '=', $user_id)
                                          ->where('contabilizado', '=', null)
                                          ->orderBy('created_at', 'DESC')
                                          ->get();

      return response()->json([
        'status' => 200,
        'albaranesEnviados' => $albaranesEnviados,

      ]);

  }
  public function getAllFacturas(){
    $factura = Factura::with(['items_factura','proyecto.usuario','ingresos'])->orderBy('fecha', 'desc')->get();
   
    return response()->json($factura, 200);
  }
  public function indexFactura($tipo){

    $factura = Factura::with(['items_factura','proyecto.usuario','ingresos'])->where('tipo',$tipo)->orderBy('fecha', 'desc')->get();
    return response()->json($factura, 200);
  }
  public function showFactura($factura_id){
    $factura = Factura::find($factura_id);

    if($factura == null) {
        return response()->json('factura no existe', 400);
    }

    $factura = Factura::where('id', $factura_id)->with('items_factura')->first();
    return response()->json($factura, 200);
  }
  public function storeFactura(Request $request){
    $validator = Validator::make($request->all(), [
        'items_factura' => 'required',
        'tipo'=>'required'
    ]);
    if ($validator->fails()) {
        return response()->json([
            'error'=>$validator->errors()
        ], 422);
    }

    /*$count_proyecto = Proyecto::where('id', $request->id_proyecto)->count();
    if($count_proyecto == 0) {
        return response()->json('Proyecto no existe', 400);
    }*/

    $anio = AnioFiscal::latest()->first();

    $factura_count = Factura::where('tipo',$request->tipo)->where('id_anio',$anio->id)->latest('nro_factura')->first(); 
    if ($factura_count){
      if(strval($factura_count->anio)== strval($anio->year)){
        $request['nro_factura'] = $factura_count['nro_factura'] + 1;
      }
      else{
        $request['nro_factura'] = 1;
      }
    }else{
      $request['nro_factura'] = 1;
    }


    $data_report = $this->generateReports($request->tipo == 1?'factura':'factura-proforma', $request->all());    
    $request['url'] = $data_report['url'];
    $request['file_name'] = $data_report['path'];

    $factura = Factura::create($request->except('items_factura'));  
    $factura->id_anio = $anio->id;
    $factura->update();
    $factura->items_factura()->createMany($request['items_factura']);

    //app('App\Http\Controllers\ApiControllers\FCMNotificationController')->SendUserNotification($proyecto->usuario_id,"Factura Generada","Factura del proyecto: ".$proyecto->nombre,"facturas");

    return response()->json('Registrado con exito', 200);
  }
  public function ChangeFacturaType(Request $request){
    $anio = AnioFiscal::latest()->first();

    $factura_count = Factura::where('tipo',$request->tipo)->where('id_anio',$anio->id)->latest('nro_factura')->first(); 
    if ($factura_count){
      if(strval($factura_count->anio)== strval($anio->year)){
        $request['nro_factura'] = $factura_count['nro_factura'] + 1;
      }
      else{
        $request['nro_factura'] = 1;
      }
    }else{
      $request['nro_factura'] = 1;
    }
    $factura_old = Factura::find($request->id);  
   

    $factura = Factura::create([
      'id_proyecto'=>$factura_old-> id_proyecto,
      'id_cliente'=>$factura_old-> id_cliente,
      'nro_factura'=>$request['nro_factura'],
      'id_anio'=>$factura_old->id_anio,
      'file_name'=>$factura_old->file_name,
      'url'=>$factura_old->url,
      'total'=>$factura_old->total,
      'descuento'=>$factura_old->descuento,
      'fecha'=>date('Y-m-d'),
      'status_iva' =>$factura_old->status_iva,
      'id_cuenta' =>$factura_old->id_cuenta,
      'tipo'=>$request->tipo,
      'notas'=>$factura_old->notas

    ]);
    $factura_old ->id_factura = $factura->id;
    $factura_old->update();
  /*$proyecto = Proyecto::where('id', $factura->id_proyecto)->with('usuario')->first();

    $data_report = $this->generateReports('factura', $factura, $proyecto);

    $factura->url = $data_report['url'];
    $factura->file_name = $data_report['path'];
    $factura->id_anio = $anio->id;
    
    $factura->update();
    $factura->items_factura()->createMany($factura_old->items_factura->toArray());*/

    $factura->items_factura()->createMany($factura_old->items_factura->toArray());
    $factura->update();
    $factura_no_change = Factura::find($factura->id);
    $data_report = $this->generateReports($factura->tipo == 1?'factura':'factura-proforma', $factura_no_change);

    /*$factura->url = $data_report['url'];
    $factura->file_name = $data_report['path'];
    $factura->id_anio = $anio->id;*/
    $factura->update([
      'url'=>$data_report['url'],
      'file_name'=>$data_report['path'],
        'id_anio'=>$anio->id,
    ]);
    
    return $factura;
    


  }

  public function updateFactura($factura_id, Request $request){  
    $validator = Validator::make($request->all(), [
        'id_proyecto' => 'required',
        'items_factura' => 'required'
    ]);
    if ($validator->fails()) {
        return response()->json([
            'error'=>$validator->errors()
        ], 422);
    }


    $factura_count = Factura::where('id_proyecto',$request->id_proyecto)->first();

   // $request['nro_factura'] = $factura_count['nro_factura']??1;

    $data_report = $this->generateReports($request->tipo == 1?'factura':'factura-proforma', $request->all());

    $request['url'] = $data_report['url'];
    $request['file_name'] = $data_report['path'];

    $factura = Factura::where('id', $factura_id)->update($request->except('items_factura'));
    $ids = [];
    foreach ($request['items_factura'] as $item) {
    
        $item_f = ItemsFactura::updateOrCreate(
            ['id' => $item['id']],
            [
                'id_factura' => $factura_id,
                'descripcion' => $item['descripcion'],
                'cantidad' => $item['cantidad'],
                'importe' => $item['importe'],
                'precio' => $item['precio'],
            ]);
            $ids[] = $item_f->id;
    }
  ItemsFactura::where('id_factura',$factura_id)->whereNotIn('id',$ids)->delete();
    return response()->json('editado con exito', 200);
  }
  public function GenerarFacturaProyectosMensuales(){
    $users = User::with('proyectosMensuales')->where('role', 2)->whereHas('proyectosMensuales')->get();
    $anio = AnioFiscal::latest()->first();
   
    $fallidos =[];
    $exitosos = [];
    foreach($users as $user){
      try{
        $factura_count = Factura::where('tipo',2)->where('id_anio',$anio->id)->latest('nro_factura')->first();
   
  
        if ($factura_count){
          if(strval($factura_count->anio)== strval($anio->year)){
            $nro_factura = $factura_count['nro_factura'] + 1;
          }
          else{
            $nro_factura= 1;
          }
        }else{
          $nro_factura = 1;
        }
        
        $factura = Factura::create([
          'id_proyecto'=>$user-> proyectosMensuales[0]->id,
          'id_cliente'=>$user->id,
          'nro_factura'=>$nro_factura,
          'id_anio'=>$anio->id,
          'file_name'=>'',
          'url'=>'',
          'total'=>'',
          'descuento'=>0,
          'fecha'=>date('Y-m-d'),
          'status_iva' =>1,
          'id_cuenta' =>1,
          'tipo'=>2,


        ]);
        $total = 0;
        foreach($user -> proyectosMensuales as $proyecto){
          $total+=$proyecto->pvp;
          $array_detalle = explode('<p>',$proyecto->detalle_servicio);
          if(count($array_detalle) >1)
          {
            $detalle_servicio = explode('</p>',$array_detalle[1])[0];
          }
          else{
            $detalle_servicio = $array_detalle[0];

          }
          ItemsFactura::create([
            'id_factura'=>$factura->id,
            'descripcion'=>$detalle_servicio,
            'cantidad'=>1,
            'importe'=>$proyecto->pvp,
            'precio'=>$proyecto->pvp,]
          );
        }
        $factura->total = $total;
        $factura->update();


        $factura_no_change = Factura::find($factura->id);
  
        $data_report = $this->generateReports($factura->tipo==1?'factura':'factura-proforma', $factura_no_change);
        $factura->url = $data_report['url'];
        $factura->file_name = $data_report['path'];
        $factura->id_anio = $anio->id;
        
        $factura->update();
        $exitosos[] =$user;
      }
      catch(\Exception $ex){
        $fallidos[] =$user;
      }
    
    }
    $admins = User::where('role',1)->get();
    $emails = [];
    foreach($admins as $admin){
        $emails[]= $admin->email;
    }
    Mail::to($emails)->send(new FacturasMensualesMail($exitosos, $fallidos));

    
  }
  public function deleteItemFactura(Request $request) {
    $items_factura = ItemsFactura::whereIn('id', $request->id_items_factura)->count();

     if($items_factura == 0) {
        return response()->json('No existe Items', 400);
    }

    ItemsFactura::destroy($request->id_items_factura);

    return response()->json('eliminado con exito', 200);
  }
  public function textrotatedImg(){
    $label = "TEXT YOU WANT TO ROTATE";
    $labelfont = 2;
    
    // pixel-width of label
    $txtsz = imagefontwidth($labelfont) * strlen($label);
    // pixel-height of label
    $txtht = imagefontheight($labelfont);
    
    // define temp image
    $labelimage = imagecreate($txtsz, $txtsz);
    $white = imagecolorallocate($labelimage, 0xFF, 0xFF, 0xFF);
    $black = imagecolorallocate($labelimage, 0x00, 0x00, 0x00);
    
    // write to the temp image
    imagestring($labelimage, $labelfont, 0, $txtsz/2 - $txtht/2, $label , $black);
    
    // rotate the temp image
    $labelimage1 = imagerotate($labelimage, 90, $white);
   
    return response(imagepng($labelimage1))  ->header('Content-Type', 'image/png'); //This will normally output the image, but because of ob_start(), it won't.
  
   
  }
  public function generateReports($file_config, $report_pdf)	{

    
   if($report_pdf['id_cliente']){
     $cliente = User::find($report_pdf['id_cliente']);
   }
   else{
    $cliente = Proyecto::find($report_pdf['id_proyecto'])->usuario;
   }
    // Start Oscar 31/10/2022
    // Get numero de cuenta
    

    $cuenta = Cuenta::where('id', $report_pdf['id_cuenta'])->first();
    $report_pdf['cuenta'] = $cuenta->cuenta;
    $report_pdf['nombre_banco'] = $cuenta->nombre_banco;
    // Recopilamos la informacion ara el nombre de la factura
    $stringfactura = $file_config.' ';
    $numfacturayear = date('y').'-';
    $numfactura = $report_pdf['nro_factura'];
    $stringcompany = ' FidiasPro';
    $stringempresa = '('.$cliente['nombre_fiscal'].')';
    $extension = '.pdf';
    $nombre_pdf = $stringfactura . $numfacturayear . $numfactura . $stringcompany . $stringempresa . $extension;
    // End Oscar 31/10/2022
    
    // copy the temp image back to the real image

    $pdf = PDF::setPaper('A4', 'portrait');

    $data = $pdf->loadView('pdf.facturas', compact('report_pdf','cliente'))->output();

    // Start Oscar 31/10/2022
    // $report_name = 'reporte_'.$file_config.'_'.uniqid().'.pdf'; // Antiguo
    $report_name = str_replace('%20', ' ', $nombre_pdf);
    // $report_name = urlencode($nombre_pdf);
    // End Oscar 31/10/2022

    $path_file_name = 'reports-all/'.$file_config.'/'.$report_name;

    Storage::disk('public')->put($path_file_name, $data);

    // Start Oscar 31/10/2022
    // $url = config('app.url').'/storage/'.$path_file_name.'#toolbar=0'; // Antiguo
    $url = config('app.url').'/storage/'.$path_file_name;
    // End Oscar 31/10/2022

    $str_path = str_replace('/', DIRECTORY_SEPARATOR, $path_file_name);

    // $path = Storage::disk('public')->path($str_path);

    return [
        'url'  => $url,
        'path' => $str_path
    ];
	}
}
