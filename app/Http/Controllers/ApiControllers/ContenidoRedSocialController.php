<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContenidoRedSocial;
use App\Models\User;
use App\Models\EstadoContenido;
use App\Helpers\FileHelper;

use Illuminate\Support\Str;
use Storage;
use File;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PostActualizadoMail;
use App\Mail\PostRecordatorioMail;
use Spatie\PdfToImage\Pdf;
class ContenidoRedSocialController extends Controller
{
  public function  getPdfFirstPageImage($nombre)
  {
      $filePath = 'storage/contenido_red/'.$nombre;
      $pdf = new Pdf($filePath);
      $image = $pdf->saveImage($filePath.'.jpg');
  
      return Response::make($image->encode('jpg'), 200, [
          'Content-Type' => 'image/jpeg',
          'Content-Disposition' => 'inline',
      ]);
  }
  public function SendNotificationMail(Request $request){
    $user=  User::find($request->id);

    Mail::to( $user->email )->send(new PostRecordatorioMail($user));

  }
  private function ChangeEstado($token,Request $request,$estado){
    $cliente = User::where('token_redes',$token)->first();
      if($cliente == null) return response('Cliente No Encontrado',400);
      $red = ContenidoRedSocial::find($request->id);
      if($red->id_cliente != $cliente->id) return response('No Autorizado',401);
      $red->id_estado = $estado;
      if($estado == 2){
        $red->observaciones = $request->observaciones;
      }
      $red->update();
    

    Mail::to( 'ireneq@fidiaspro.com' )->send(new PostActualizadoMail($cliente,$estado == 1));
      return $red;
  }
    public function Aceptar($token,Request $request)
    {
      return $this->ChangeEstado($token,$request,1);
    
    }
    public function Rechazar($token,Request $request)
    {
      return $this->ChangeEstado($token,$request,2);
    
    }
    public function addtoken($id){
        $cliente = User::find($id);
        if($cliente == null) return response('Cliente No Encontrado',400);
        $cliente->token_redes = Str::random(20);
        $cliente->update();
        return $cliente;
    }
    public function getPendienteByToken($token){
      $cliente = User::where('token_redes',$token)->first();
      if($cliente == null) return response('Cliente No Encontrado',400);
      $red = ContenidoRedSocial::with(['Estado'])->orderBy('created_at', 'DESC')->where('id_cliente',$cliente->id)->where('id_estado',3)->get();
      foreach($red as $item){
        $this->seturl($item);

      }
      return $red;
    }
      public function getAllbyToken($token){
        $cliente = User::where('token_redes',$token)->first();
        if($cliente == null) return response('Cliente No Encontrado',400);
        $red = ContenidoRedSocial::with(['Estado'])->orderBy('created_at', 'DESC')->where('id_cliente',$cliente->id)->get();
        foreach($red as $item){
          $this->seturl($item);
  
        }
        return $red;
      } 
      private function seturl($contenido){
        $archivos = $contenido->archivos()->pluck('url');
        $res=  [ ];
        foreach($archivos as $archivo){
          $res[] = url('storage/contenido_red/'.$archivo);
        }
        $contenido->archivos = $res;
      }
      public function getContenidoById($id){
        $contenido = ContenidoRedSocial::find($id);
        $this->seturl($contenido);
        return response()->json($contenido,200);
      }
    
      public function deleteContenido($id){
        $contenido = ContenidoRedSocial::find($id);
        $contenido->archivos()->delete();
        $contenido->delete();
        return response()->json('Contenido eliminado', 200);
      }
      public function getEstados(){
        return EstadoContenido::all();
      }
      public function changeContenidoActive(Request $request){
        $contenido = ContenidoRedSocial::find($request->id);
        $contenido->id_estado = $request->id_estado;
        $contenido->observaciones = $request->observaciones;
        $contenido->save();
        return response()->json('Estado actualizado', 200);
      }
    
      public function saveContenido(Request $request){

        try {
          $contenido = ContenidoRedSocial::updateOrCreate(['id' => $request->id], $request->toArray());
          if($request->archivos){
            $arch = [];
            $urls = [];
            foreach($request->archivos as $archivo){
              if($archivo[0] == 'h'){
                $urls[] = str_replace(url('storage/contenido_red').'/','', $archivo);
              }
              else{
                $arch[] = $archivo;
              }
            }
            $contenido->archivos()->whereNotIn('url',$urls)->delete();
            $fileHelper = new FileHelper($contenido);
            $fileHelper->guardarArchivos64($arch, 'contenido_red');
         }
    
         
          return response()->json($contenido, 200);
        }
        catch (Exception $exception) {
          return response()->json('error', 301);
        }
      }
 
}
