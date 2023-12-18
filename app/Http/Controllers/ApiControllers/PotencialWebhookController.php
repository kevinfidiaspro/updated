<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FacebookForm;
use App\Models\Proyecto;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPotencialMail;
class PotencialWebhookController extends Controller
{
    public function FormularioFidias(Request $request){
   
        $form = FacebookForm::where('name','like','%'.$request->campanya.'%')->first();
        if($form == null){
            $id = null;
            $facebook = true;
            while($facebook){
                $id = Str::random(16);
                $facebook = FacebookForm::find($id);
            }
            $form = FacebookForm::create([
                "id"=>$id,
                "locale"=>'es_ES',
                "name"=>$request->campanya,
                'page_id'=>104140814678268,
                'id_servicio'=>5
            ]);
        }
        $user = User::where('email',$request['your-email'])->first();
        if($user == null){
            $user = new User();
            $password = Str::random(10);
            $user->fill([
                'nombre'=>$request['your-name'],
                'user_id'=>0,
                'role'=>2,
                'telefono'=>$request['tel-351'],
                'password'=>$password,
                'provincia_id'=>0,
                'email'=>$request['your-email'],
            ]);
            $user->saveOrFail();    
        }
        $user->update(['observaciones'=>('¿Tienes empresa o eres autónomo?: '. $request['radio-341']).' ¿Tienes el Kit Digital concedido?: '.$request['radio-735']]);
        $proyecto = Proyecto::where('usuario_id',$user->id)->first();
        if($proyecto  == null){
            $potencial_guardado = Proyecto::updateOrCreate(['usuario_id'=> $user->id,'id_lead_form'=> $form->id??null], [
                'usuario_id'       => $user->id,
                'servicio_id'      => $form != null?($form->id_servicio??5):5,
                'id_lead_form'     => $form != null?$form->id:null,
                'fecha_alta'       => date('Y-m-d H:i:s'),
                'detalle_servicio' => $form != null?$form->name:$request->campanya,
                'estado_id'        => 3,
                'pvp'              => 0,
                'pvp_gasto'        => 0,
                'id_estado_potencial'=> 1
                ]);
                $this->SendPotencialEmail($user,$form);
        }    
    }
    private function SendPotencialEmail($cliente,$form){
        try{
            /*$admins = User::where('role',1)->get();
            $emails = [];
            foreach($admins as $admin){
                $emails[]= $admin->email;
            }*/
            Mail::to('info@fidiaspro.com' )->send(new NewPotencialMail($cliente, $form));

        }catch(\Exception $ex){

        }
    }
}
