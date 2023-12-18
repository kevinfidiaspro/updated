<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\User;
use App\Models\FCMToken;
use Illuminate\Http\Request;
use App\Mail\User\UpdateUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Users\UserRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use GuzzleHttp\Client;
//use Dotenv\Validator;
use Illuminate\Support\Facades\Validator;

class FCMNotificationController extends Controller
{
  //funcion basica para enviar notificaciones
  public function SendNotificationTo($to, $title, $body,$destino){
    $FCMKey = 'key=AAAAQb8G5yE:APA91bHsCi-GnoTtJftCJdyJIVSJz2zn_Xc3FdzdsmCG4GC2XVbBz_WJmz5cXyB643-qTruV6w9TARMnYx8RX8OciGiD5gXOTOPVz2-8i5bMk0Zz-ABVNZM61HuALnSj3FG4-3yThijW';

    $client = new Client(['headers'=>['Content-Type' => 'application/json','Authorization'=>$FCMKey]]);
    $client ->post('https://fcm.googleapis.com/fcm/send',[
      'body'=>json_encode(["to"=>$to,
      
      "notification"=> ([
        "title"=> $title,
        "body"=> $body
      ]),
  
     "data"=> (["destino"=>$destino,"test"=>"000000"
     ])
      
        ])
    ]);
  }
  //Enviar notificacion a un usuario especifico, con su id de usuario
  public function SendUserNotification($user_id, $title, $body,$destino){
    $tokens = FCMToken::all()->where('user_id',$user_id);
    foreach($tokens as $token){
     $this->SendNotificationTo($token->token,$title,$body,$destino);
    }
  }
  //funcion para enviar notificaciones push a todos los usuarios segun el tema 
  public function SendNotificationToAll($topic, $title, $body,$destino){
    $to = '/topics'.'/'.$topic;
   $this->SendNotificationTo($to, $title, $body,$destino);
  }
  //Funcion Para almacenar el token, es llamada en la funcion Login en el AuthController
  public function StoreToken($id,$fcmToken){
    if( $id == null ||  $fcmToken == null){
      return response()->json(['message' => ['Datos Invalidos']], 401);
    }
    $tokens = FCMToken::where('token',$fcmToken)->get();
    if(count($tokens)==0){
      FCMToken::create([
        'user_id'=>$id,
        'token'=>$fcmToken
      ]);
    }
   
  }
  /*
  
            Funciones de api
  
  */
  //funcion de notificacion a todos
  public function sendAllNotification(Request $request){
    $this->SendNotificationToAll("promociones",$request -> titulo,$request -> cuerpo,$request-> destino);
  }
  //funcion de notificacion a un usuario especifico
  public function sendNotificationToUser(Request $request){
    $this->SendUserNotification($request->id,$request -> titulo,$request -> cuerpo,$request-> destino);
  }
  //funcion para guardar el token
  public function StoreTokenApi(Request $request){
    $this->StoreToken($request->id,$request->token);
  }
  
}
