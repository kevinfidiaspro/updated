<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Resources\PresupuestoResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\AnioFiscal;
use App\Models\Cuenta;
use App\Models\Recibo;
use Storage;
use App\Models\Proyecto;
use App\Models\Albaranes\AlbaranesEnviado;
use App\Models\Presupuesto;
use App\Models\ItemsPresupuesto;
use PDF;
use Validator;

class NewPresupuestoController extends Controller
{
  public function DuplicatePresupuesto($id){
    $Presupuesto_old = Presupuesto::find($id);
    if($Presupuesto_old != null){
      $anio = AnioFiscal::latest()->first();
      $Presupuesto_count = Presupuesto::latest('id')->first();
     
    
      if ($Presupuesto_count){
        if(strval($Presupuesto_count->anio)== strval($anio->year)){
          $Presupuesto_old['nro_presupuesto'] = $Presupuesto_count['nro_presupuesto'] + 1;
        }
        else{
          $Presupuesto_old['nro_presupuesto'] = 1;
        }
      }else{
        $Presupuesto_old['nro_presupuesto'] = 1;
      }
      
      $presupuesto = Presupuesto::create([
        'id_proyecto'=>$Presupuesto_old-> id_proyecto,
        'nro_presupuesto'=>$Presupuesto_old->nro_presupuesto,
        'id_anio'=>$Presupuesto_old->id_anio,
        'file_name'=>$Presupuesto_old->file_name,
        'url'=>$Presupuesto_old->url,
        'total'=>$Presupuesto_old->total,
        'descuento'=>$Presupuesto_old->descuento,
        'fecha'=>date('Y-m-d'),
        'status_iva' =>$Presupuesto_old->status_iva,
        'id_cuenta' =>$Presupuesto_old->id_cuenta,
      ]);

      $proyecto = Proyecto::where('id', $presupuesto->id_proyecto)->with('usuario')->first();

      $data_report = $this->generateReports('presupuesto', $presupuesto, $proyecto);

      $presupuesto->url = $data_report['url'];
      $presupuesto->file_name = $data_report['path'];
      $presupuesto->id_anio = $anio->id;
      
      $presupuesto->update();
      $presupuesto->items_presupuesto()->createMany($Presupuesto_old->items_presupuesto->toArray());

    }else{
      return response("Presupuesto no encontrado",404);
    }
  }
  
  public function getPresupuestosByProyecto($id_proyecto){
    $presupuesto = Presupuesto::with('items_presupuesto','proyecto.usuario')->where('id_proyecto',$id_proyecto)->orderBy('fecha', 'desc')->get();
    return response()->json($presupuesto, 200);
  }
 

  public function indexPresupuesto(){

    $presupuesto = Presupuesto::with('items_presupuesto','proyecto.usuario')->orderBy('fecha', 'desc')->get();
    return response()->json($presupuesto, 200);
  }
  public function showPresupuesto($presupuesto_id){
    $presupuesto = Presupuesto::with(['items_presupuesto'])->find($presupuesto_id);

    if($presupuesto == null) {
      $presupuesto = Presupuesto::with(['items_presupuesto'])->where('id_proyecto',$presupuesto_id)->first();
    if($presupuesto == null){
      return response()->json('Presupuesto no existe', 400);

    }
    }

    //$presupuesto = Presupuesto::where('id', $presupuesto_id)->with('items_presupuesto')->first();
    return response()->json($presupuesto, 200);
  }
  public function storePresupuesto(Request $request){
    $validator = Validator::make($request->all(), [
        'id_proyecto' => 'required',
        'items_presupuesto' => 'required'
    ]);
    if ($validator->fails()) {
        return response()->json([
            'error'=>$validator->errors()
        ], 422);
    }

    $count_proyecto = Proyecto::where('id', $request->id_proyecto)->count();
    if($count_proyecto == 0) {
        return response()->json('Proyecto no existe', 400);
    }

    $anio = AnioFiscal::latest()->first();

    $presupuesto_count = Presupuesto::latest('id')->first(); 
    if ($presupuesto_count){
      if(strval($presupuesto_count->anio)== strval($anio->year)){
        $request['nro_presupuesto'] = $presupuesto_count['nro_presupuesto'] + 1;
      }
      else{
        $request['nro_presupuesto'] = 1;
      }
    }else{
      $request['nro_presupuesto'] = 1;
    }

    $proyecto = Proyecto::where('id', $request->id_proyecto)->with('usuario')->first();

    $data_report = $this->generateReports('presupuesto', $request->all(), $proyecto);    
    $request['url'] = $data_report['url'];
    $request['file_name'] = $data_report['path'];

    $presupuesto = Presupuesto::create($request->except('items_presupuesto'));  
    $presupuesto->id_anio = $anio->id;
    $presupuesto->update();
    $presupuesto->items_presupuesto()->createMany($request['items_presupuesto']);

    app('App\Http\Controllers\ApiControllers\FCMNotificationController')->SendUserNotification($proyecto->usuario_id,"Presupuesto Generado","presupuesto del proyecto: ".$proyecto->nombre,"presupuestos");

    return response()->json('Registrado con exito', 200);
  }
  public function updatePresupuesto($presupuesto_id, Request $request){  
    $validator = Validator::make($request->all(), [
        'id_proyecto' => 'required',
        'items_presupuesto' => 'required'
    ]);
    if ($validator->fails()) {
        return response()->json([
            'error'=>$validator->errors()
        ], 422);
    }

    $count_proyecto = Proyecto::where('id', $request->id_proyecto)->count();
    if($count_proyecto == 0) {
        return response()->json('Proyecto no existe', 400);
    }

    $presupuesto_count = Presupuesto::where('id_proyecto',$request->id_proyecto)->first();

    $request['nro_presupuesto'] = $presupuesto_count['nro_presupuesto']??1;

    $proyecto = Proyecto::where('id', $request->id_proyecto)->with('usuario')->first();    
    $data_report = $this->generateReports('presupuesto', $request->all(), $proyecto);

    $request['url'] = $data_report['url'];
    $request['file_name'] = $data_report['path'];

    $presupuesto = Presupuesto::where('id', $presupuesto_count->id)->update($request->except('items_presupuesto'));

    foreach ($request['items_presupuesto'] as $item) {
        ItemsPresupuesto::updateOrCreate(
            ['id' => $item['id']],
            [
                'id_presupuesto' => $presupuesto_count->id,
                'descripcion' => $item['descripcion'],
                'cantidad' => $item['cantidad'],
                'importe' => $item['importe'],
                'precio' => $item['precio'],
            ]);
    }

    return response()->json('editado con exito', 200);
  }
  public function deleteItemPresupuesto(Request $request) {
    $items_presupuesto = ItemsPresupuesto::whereIn('id', $request->id_items_presupuesto)->count();

     if($items_presupuesto == 0) {
        return response()->json('No existe Items', 400);
    }

    ItemsPresupuesto::destroy($request->id_items_presupuesto);

    return response()->json('eliminado con exito', 200);
  }

  public function generateReports($file_config, $report_pdf, $proyecto)	{

    // Start Oscar 31/10/2022
    // Get numero de cuenta
    $cuenta = Cuenta::where('id', $report_pdf['id_cuenta'])->first();
    $report_pdf['cuenta'] = $cuenta->cuenta;
    $report_pdf['nombre_banco'] = $cuenta->nombre_banco;
    // Recopilamos la informacion ara el nombre de la presupuesto
    $stringpresupuesto = $file_config;
    $numpresupuestoyear = date('y').'-';
    $numpresupuesto = $report_pdf['nro_presupuesto'];
    $stringcompany = 'FidiasPro';
    $stringempresa = '('.$proyecto['usuario']['nombre_fiscal'].')';
    $extension = '.pdf';
    $nombre_pdf = $stringpresupuesto . $numpresupuestoyear . $numpresupuesto . $stringcompany . $stringempresa . $extension;
    // End Oscar 31/10/2022
    
    // copy the temp image back to the real image

    $pdf = PDF::setPaper('A4', 'portrait');

    $data = $pdf->loadView('pdf.presupuestos', compact('report_pdf','proyecto'))->output();

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
