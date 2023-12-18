<?php

namespace App\Http\Controllers\ApiControllers;

use Kutia\Larafirebase\Facades\Larafirebase;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ChatResource;
use Illuminate\Http\Request;
use App\Models\ChatMensaje;
use App\Models\Chat;

class ChatController extends Controller
{
    /*Aqui va a ir una paginacion*/
    public function getAllChats(){
      $chats = ChatResource::collection(Chat::with('user')->get());
      return response()->json($chats, 200);
    }

    public function getChat($id = NULL, Request $request){
      $user = $request->user();

      if($user->role == 2){
        return $this->getUserChat($user)->load('mensajes');
      }

      $chat = Chat::with('mensajes')->where('id', $id)->get()->first();

      return response()->json($chat, 200);
    }

    public function guardarMensaje($chat_id, Request $request){
      $user = $request->user();

      $chat = Chat::find($chat_id);

      $to_user = $user->role == 1 ? $chat->user_id : NULL;

      $to_model = $user->role == 1 ? $chat : $user->chat;

      $mensaje = $to_model->mensajes()->create(['mensaje' => $request->mensaje, 'from_user' => $user->id, 'to_user' => $to_user]);
      app('App\Http\Controllers\ApiControllers\FCMNotificationController')->SendUserNotification($to_user,"Mensaje nuevo en el chat",$request->mensaje,"chat");
      return response()->json($mensaje, 200);
    }

    /**
     * Retornar chat o crear si el usuario no tiene uno creado
     *
     * @param  \Illuminate\Support\Facades\Auth  $user
     * @return \App\Models\Chat
     */
    private function getUserChat($user){
      return $user->chat()->firstOrCreate(['user_id' => $user->id], ['user_id' => $user->id]);
    }

    public function setSeen($chat_id, Request $request){
      $user = $request->user();

      if($user->role == 2){
        return ChatMensaje::where('chat_id', $chat_id)->where('to_user', $user->id)->update(['visto' => true]);
      }
      return ChatMensaje::where('chat_id', $chat_id)->whereNull('to_user')->update(['visto' => true]);
    }

    private function enviarNotificacion($device_token){
      return Larafirebase::fromArray(['title' => 'Nuevo mensaje', 'body' => ''])->sendNotification($device_token);
    }
}
