<?php

namespace App\Http\Controllers\ApiControllers;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Cliente;
use App\Models\Direccion;
use App\Models\User;
use App\Models\Rol;
use App\Models\FacebookLead;
use App\Models\FacebookToken;
use App\Models\FacebookForm;
use App\Models\FacebookLeadFields; 
use App\Models\Servicio;
use App\Models\Proyecto;
use App\Models\FacebookPage;
use App\Models\CicloCreacionPotenciales;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPotencialMail;
class FacebookWebhookController extends Controller
{
    public function CallbackVerification(Request $request){
        if($request->isMethod('post')){
           return $this->WeebHookFunction($request);
        }
        else{
            $hubMode = $request->hub_mode;
            $hubChallenge = $request->hub_challenge;
            $hubVerifyToken = $request->hub_verify_token;
            return $hubChallenge;
        }
   
    }
    public function getFormularios(Request $request){
        $formularios = FacebookForm::with(['Servicio'])->whereHas('Leads',function($query) use ($request){
            $query->where('page_id',$request->id);
        })->orWhere('page_id',$request->id)->get();
        return $formularios;
    }
    public function getFormulariosActivos(Request $request){
        $formularios = FacebookForm::with(['Servicio'])->where('status','like','ACTIVE')->whereHas('Leads',function($query) use ($request){
            $query->where('page_id',$request->id);
        })->get();
        return $formularios;
    }
    public function updateFormulario(Request $request){
        $formulario = FacebookForm::find($request->id);
        $formulario-> id_servicio = $request->id_servicio;
        $formulario-> id_page_sendinblue = $request->id_page_sendinblue;
        $formulario->field_phone = $request->field_phone;
        $formulario->field_name = $request->field_name;
        $formulario->field_email = $request->field_email;

        $formulario->update();
        return $formulario;
    }
    public function getFormulario($id){
        $formulario = FacebookForm::with(['Servicio'])->find($id);
        return $formulario;
    }
    private function WeebHookFunction(Request $request){
            foreach($request-> entry as $element){
                foreach($element['changes'] as $change){
                    if($change['field']??"" == 'leadgen'){
                        $id = null;
                        if(isset($change['value']['leadgen_id'])) $id = $change['value']['leadgen_id'];
                        $lead = FacebookLead::updateOrCreate(['leadgen_id'=>$id],$change['value']);
                        $this->getLeadData($lead);
                        $this->sortData($lead);
                    }
                }
            }
      
    }
    private function sortData($lead){
        switch($lead->page_id){
            case '104140814678268'; //FIDIAS
            $this->createPotencial($lead);
            $this->AddSendinBlue($lead);
            break;
            case '107032871819050'; //VBS SERVICIOS
            $this->saveVBS($lead);
            break;
        }
    }
    private function saveVBS($lead){
        $form = $lead->form;
        if($form == null){
            $form = FacebookForm::where('id',$lead->form_id)->first();
        }
        $cliente = [
            "id_role"=>6,
            "nombre"=>$lead->value[$form->field_name??'full_name']??"Sin Nombre",
            "apellidos"=>"lead",
            "dni"=>"0",
            "cif"=>"0",
            "nombre_fiscal"=>$lead->value[$form->field_name??'full_name']??"Sin Nombre",
            "telefono"=>$lead->value[$form->field_phone??'phone']??0,
            "email"=>$lead->value[$form->field_email??'email'],
            "id_agente"=>0,
            "num_cliente"=>"0",
            "observaciones"=>"facebook",
            "archivo"=>[],
            "id_estado"=>1,
            "fb_form_id"=>$lead->form_id,
            "fb_form_name"=>$form->name
        ];
            
        $client = new \GuzzleHttp\Client();
        ///https://app.vbservicios.es/api/saveclientes-facebook
        $response = $client->request('POST', 'https://app.vbservicios.es/api/saveclientes-facebook', [
            'form_params' => $cliente 
        ]);  
        return $response;
    }
 
    public function getLead(Request $request){
        $lead = FacebookLead::where('leadgen_id',$request->id)->first();
        return $lead;
    }
    public function getLeadFieldData(){
        $leads = FacebookLeadFields::all();
        return $leads;

    }
    public function FormFields(Request $request){
        $LeadFields = FacebookLead::where('form_id',$request->id)->with('leadData')->has('leadData')->first();
        if($LeadFields == null) return [];
        return $LeadFields->leadData;
    }
    public function FixLeads(){
        $leads = [FacebookLead::with('Form')->where('page_id','104140814678268')->first()];
        $failed = [];
       // return $leads;
        //return $leads;
        foreach($leads as $lead){
            try{
                
                if(count($lead->value) != 0) 
                {
                    $this->getLeadData($lead); 
                }
                $this->sortData($lead);
            }
            catch(\Exception $ex){
                $failed[] = $lead->Form;
            }
        }
        return response()->json(['failed'=>$failed,'all'=>$leads]);
    }
    public function TestSendinBlue($id){
        $Form = FacebookLead::find($id);
        return $this->AddSendinBlue($Form);
    }
    private function AddSendinBlue($lead){
        try{
            $form = FacebookForm::find($lead->form_id);
            return app('App\Http\Controllers\ApiControllers\SendinBlueController') -> AddUser($lead->value[$form->field_email??'email'],$form,$lead->value[$form->field_name??'full_name']??"Sin Nombre");
        }catch(\Exception $ex){

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
    public function GetLeadsFromForm(Request $request){
        $leads = FacebookLead::where('form_id',$request->form_id)->get();
        
        return response()->json(['form'=> $leads[0]->form,'leads'=>$leads]);
    }
    public function FixLead(Request $request){
        $lead = FacebookLead::find($request->id);
        
        try{
            $this->getLeadData($lead);

        }catch(\Exception $ex){

        }
        $this->sortData($lead);
        $leads = FacebookLead::where('form_id',$request->form_id)->get();

        return $leads;
    }
    public function getCurrentPotencial(){
        $empleados = User::where('role',9)->orWhere('role',8)->orderBy('id')->get();
        $ciclo =  CicloCreacionPotenciales::first();
        if($ciclo == null){
            $empleado = $empleados->first();

            $ciclo = CicloCreacionPotenciales::create(['id_empleado'=>$empleado->id,'cantidad'=>1]);
        }else{
            $empleado = $empleados->first();
            $next_empleado = $empleados->first();
            $cantidad = 1;
            for($i = 0; $i< count($empleados); $i++){
                $empleado_tmp  = $empleados[$i];
                if($empleado_tmp->id < $ciclo->id_empleado){
                 
                }else if($empleado_tmp->id == $ciclo->id_empleado){
                    $empleado = $empleado_tmp;
                    if(($i +1 )>= count($empleados)){
                        $next_empleado = $empleados[0];
                    }
                    else{
                        $next_empleado = $empleados[$i+1];
                    }
                    if($empleado->jornada_completa && $ciclo->cantidad == 1){
                        $cantidad = 2;
                    }
                }else if($empleado == null){
                    $next_empleado = $empleado_tmp;
                    break;
                }
            }
            if($cantidad == 2){
                $ciclo->update(['id_empleado'=>$empleado->id,'cantidad'=>2]);
            }
            else{
                $ciclo->update(['id_empleado'=>$next_empleado->id,'cantidad'=>1]);
            }
     
        
        }
        $ciclo->Empleado;
        return $ciclo;
    }
    private function createPotencial($lead){
        $form = FacebookForm::find($lead->form_id);
        $user = User::where('email',$lead->value[$form->field_email??'email'])->first();
        if($user == null){
            $user = new User();
            $password = Str::random(10);
            $user->fill([
                'nombre'=>$lead->value[$form->field_name??'full_name']??"Sin Nombre",
                'user_id'=>0,
                'role'=>2,
                'telefono'=>$lead->value[$form->field_phone??'phone']??0,
                'password'=>$password,
                'provincia_id'=>0,
                'email'=>$lead->value[$form->field_email??'email'],
            ]);
            $user->saveOrFail();
        }
        try{
            $ciclo = $this->getCurrentPotencial();
            $user->update(['vendedor_id'=>
            566//$ciclo['id_empleado']
        ]);
        }catch(\Exception $ex){

        }
      
        $proyecto = Proyecto::where('usuario_id',$user->id)->first();
        if($proyecto  == null){
            $potencial_guardado = Proyecto::updateOrCreate(['usuario_id'=> $user->id,'id_lead_form'=> $form->id], [
                'usuario_id'       => $user->id,
                'servicio_id'      => $form->id_servicio,
                'id_lead_form'     => $form->id,
                'fecha_alta'       => date('Y-m-d H:i:s'),
                'detalle_servicio' => $form->name,
                'estado_id'        => 3,
                'pvp'              => 0,
                'pvp_gasto'        => 0,
                'detalles_gasto'   => 'Facebook Lead',
                'id_estado_potencial'=> 1
              ]);
              $this->SendPotencialEmail($user,$form);
        }
      

      
    }
    public function FacebookAddAppRequest(Request $request){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://graph.facebook.com/'.$request->page_id.'/subscribed_apps?access_token='.$request->page_token,  ['form_params' => ['subscribed_fields'=>'leadgen'] ]);
        $form_data =  json_decode($response->getBody()->getContents(),true);
        return $form_data;
    }
    public function FacebookGetPageAccessToken(Request $request){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://graph.facebook.com/v14.0/'.$request->page_id.'?fields=access_token&access_token='.$request->long_lived_token);
        $form_data =  json_decode($response->getBody()->getContents(),true);
        return $form_data;
    }
    public function FacebookGetLongLivedToken(Request $request){
        $client = new \GuzzleHttp\Client();
        $url = 'https://graph.facebook.com/v14.0/oauth/access_token?grant_type=fb_exchange_token&client_id='.$request->app_id.'&client_secret='.$request->app_secret.'&fb_exchange_token='.$request->short_user_token;
        $response = $client->request('GET', $url );
        $form_data =  json_decode($response->getBody()->getContents(),true);
        return $form_data;
    }
    public function SavePage(Request $request){
        $page = FacebookPage::updateOrCreate(['id'=>$request->id],[
            "id"=>$request->id,
            "name"=>$request->name,
            "sendinblue_key"=>$request->sendinblue_key,
        ]);
        $id = null;
        if(isset($request->facebook_token['id'])){
            $id = $request->facebook_token['id'];
        }
        $token = FacebookToken::updateOrCreate(['id'=>$id],[
            "nombre"=>'',
            "token"=>$request->token,
            'page_id'=>$request->id,
        ]);

    }
    public function GetPage($id){
       
        return FacebookPage::where('id',$id)->first();
    }
    public function GetPages(){
       
        return FacebookPage::all();
    }
    private function getLeadData( $lead ){
     
        $token = $lead->Token;
        if($token && $lead){
            $form = FacebookForm::find($lead->form_id);
            if($form == null){
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://graph.facebook.com/v14.0/'.$lead->form_id.'?access_token='.$token->token);
                $form_data =  json_decode($response->getBody()->getContents(),true);
                
                $form = FacebookForm::create($form_data);
                $servicio = Servicio::updateOrCreate(['id_lead_form'=>$form_data['id']],[
                    'nombre'=>$form_data['name'],
                    'id_lead_form'=>$form_data['id']
                ]);
                //return $form;

            }

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://graph.facebook.com/v14.0/'.$lead->leadgen_id.'?access_token='.$token->token);
            $lead_data =  json_decode($response->getBody()->getContents(),true);
            if(isset($lead_data['field_data'])){
                foreach($lead_data['field_data'] as $field){
                    FacebookLeadFields::updateOrCreate(["lead_id"=>$lead->leadgen_id,
                    "name"=>$field['name'],],[
                        "lead_id"=>$lead->leadgen_id,
                        "name"=>$field['name'],
                        "value"=>$field['values'][0],
                    ]);
                }
            }
        }

        
    }
}
