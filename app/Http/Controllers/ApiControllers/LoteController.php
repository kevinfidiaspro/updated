<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\LoteFacturas;
use App\Models\NroFactura;
use App\Models\FacturaRecibida;
use ZipArchive;
use File;
use App\Traits\Basic;
use App\Models\User;
use App\Jobs\MailLotesJob;

class LoteController extends Controller
{
     public function enviarFacturas(Request $request, $idUser){

          $user = User::where('id', $idUser)->first();
          $emails = $request->form['emails']; //array
          $description = $request->form['descripcion'];
          $tipo_factura = $request->form['tipo_factura']['tipo'];

           $data = [
               'user' => $user,
               'emails' => $emails,
               'des' =>  $description, 
               'tipo' => $tipo_factura,
          ];

          dispatch(new MailLotesJob($data));


          // return $data;
          // if (!$request->filled('email')) {
          //    return response()->json('email esta vacio', 301);
          // }
          // Mail::to($request->email)->send(new LoteFacturas());
          return response()->json('enviado con exito', 200);
     }

     public function getFacturasByRango(Request $request, $user_id){

          return  $this->switchFact($request, $user_id);

     }

     private function crearZip($lista_facturas, $userId,  $tipo){
          $zip = new ZipArchive;
          $fileName = '';
          if($tipo == 'Facturas Enviadas'){
               $fileName = 'facturas_enviadas.zip';
          }

          if($tipo == 'Todas'){
               $fileName = 'facturas_enviadas.zip';
          }
          /*return response()->json([
               'fileName' => storage_path('app/public/lotes/userId_' . $userId . '/' . $fileName),
               'zip' => $zip
          ], 200);*/
          if($zip->open(storage_path('app/public/lotes/userId_' . $userId . '/' . $fileName), ZipArchive::CREATE) === TRUE){

            foreach ($lista_facturas as $key => $factura) {

               $file_name = $factura->recibo['factura_url'];

               if(Storage::disk('recibos')->exists($file_name)){

                    $contents = storage_path("app/public/recibos/{$file_name}");

                    $zip->addFile($contents, $file_name);
               }
             }
             $zip->close();
         }
          return response()->json([
               'fileName' => $fileName,
               'zip' => $zip
          ], 200);
     }


     public function switchFact($request, $userId){

          $tipoFact = $request->tipo;
          $enviadas =null;
          $recibidas =null;
          $todas =null;
          switch ($tipoFact) {
               case "Todas":
                    $todas = $this->caseTodas($request, $userId);
                    break;
               case "Facturas Enviadas":
                    $enviadas = $this->caseEnviadas($request, $userId);
                    break;
               case "Facturas Recibidas":
                    $recibidas= $this->caseRecibidas($request, $userId);
                    break;
          }


          return response()->json([

               'enviadas' => $enviadas,
               'recibidas' => $recibidas,
               'todas' => $todas,
          ]);
       }


     public function caseTodas($request, $userId){

          $enviadas      = $this->caseEnviadas($request, $userId);
          $recibidas     = $this->caseRecibidas($request, $userId);

          return response()->json([
               'enviadas' => $enviadas,
               'recibidas' => $recibidas,
               'todas' => true
          ]);
     }

     public function caseEnviadas($request, $userId){

          if(Storage::disk('lotes')->exists('facturas_enviadas.zip')){
             Storage::disk('lotes')->delete('facturas_enviadas.zip');
          }

          if ($request->has(['desde', 'hasta'])) {
               $facturas = NroFactura::where('user_id', '=', $userId)->whereHas('recibo', function($query) use ($request){
                  $query->whereBetween('fecha', [$request->desde, $request->hasta]);
               })->with('recibo')->get();

               $archivo = $this->crearZip($facturas,  $userId , $request->tipo);

               
               return response()->json([
                    'archivo' => $archivo,
                    'enviadas' => true,
                    'facturasEnviadasGet' => $facturas,
               ],200);
          }
          return response()->json('error', 301);
         
     }

     public function caseRecibidas($request, $userId){
          $pathServe = Basic::pathServer();// return http://apase.test/
          $facRecibidas = FacturaRecibida::
                where("fecha",">=",$request->desde)
                ->where("fecha","<=",$request->hasta)
                ->orderBy('id', 'desc')->get();

          $arrayImagenes = [];
          foreach($facRecibidas as $facturaRecibida){
               $arrayImagenes[] = json_decode($facturaRecibida->imagen);
          }
          $imagesNames = [];
          foreach($arrayImagenes as $imagenName){
               foreach($imagenName as $image){
                   $imagesNames[] = $image;
               }
               
          }

          $zip = new ZipArchive;
          $fileName = 'facturas_recibidas.zip';
          if($zip->open(storage_path('app/public/lotes/userId_'. $userId . '/'  . $fileName), ZipArchive::CREATE) === TRUE){
            foreach ($imagesNames as  $factura) {
               $archivoEnApp = $pathServe . 'storage/app/public/documentos/userId_'. $userId. '/factura_recibidas/'. $factura;
               if(file_exists($archivoEnApp)){
                    $file_name = $factura;
                    $contents = storage_path('app/public/documentos/userId_'. $userId. '/factura_recibidas/'. $factura);
                    $zip->addFile($contents, $file_name);
               }
              
             }
             $zip->close();
          }
         
          return response()->json([
               'recibidas' => true,
               'archivo' => $fileName,
               'facturasRecibidasGet' => $imagesNames,
          ]);
     }





}
