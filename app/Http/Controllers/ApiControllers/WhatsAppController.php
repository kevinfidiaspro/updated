<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\WhatsAppPlantilla;
class WhatsAppController extends Controller
{
    public function getPlantillas(){
        return WhatsAppPlantilla::all();
    }
    public function getPlantilla($id){
        return WhatsAppPlantilla::find($id);
    }
    public function deletePlantilla($id){
        WhatsAppPlantilla::find($id)->delete();
        return $this->getPlantillas();
    }
    public function savePlantilla(Request $request){
        return WhatsAppPlantilla::updateOrCreate(['id'=>$request->id],$request->all());
    }
    public function sendMesssage($to){
        $token = "EAAFvQNeQNX8BALvTZBeruNRB9Vh3TRvXXniSWvpvaqNMbJBnvNZBsozp1snLADEFH2FU7xHVx296aIq8RNCZC7lERLLhaOKyx9wgHPZCyrOvKuLC7pYWF9sgF0GZCfhtq3naSZB7HUc58BEF2hhReTKV9VVlD9yogWPIsDm4dME3YymOFHETGC1K2CEhmWPZBnfXuneUmjZCQgZBZBZCo1ZA1oNbbVZAvSIix044ZD";
        $number_id = "108374492251683";
        $client = new \GuzzleHttp\Client();
        ///https://app.vbservicios.es/api/saveclientes-facebook
        $response = $client->request('POST', 'https://graph.facebook.com/v16.0/'.$number_id.'/messages', [
            'form_params' => [
                "messaging_product"=>"whatsapp",
                "to"=>$to,
                "type"=> "template", 
                "template"=> [ "name"=> "hello_world", "language"=> [ "code"=> "en_US" ] ]
            ] ,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
 
        ]);  
        return 'Mensaje Enviado Correctamente';
    }
}
