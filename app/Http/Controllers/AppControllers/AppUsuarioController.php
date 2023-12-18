<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Mail\User\RecoverPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Mail;
use Str;

class AppUsuarioController extends Controller
{
  public function getAvatarByUserId($user_id){
    $user = User::where('id', $user_id)->get(['avatar'])->first();
    if($user->avatar == null){
      $rutaImagen = "https://plataforma.fidiaspro.com/default.png";
      $contenidoBinario = file_get_contents($rutaImagen);
      $imagenComoBase64 = base64_encode($contenidoBinario);
      //$user->avatar = "https://plataforma.fidiaspro.com/default.png";
      $user->avatar =  $imagenComoBase64;
    }
    
    return response()->json($user, 200);
  }

  public function recover(Request $request){
    $user = User::where('email', $request['email'])->get()->first();

    if(!$user){
      return response()->json([
        'status' => false
      ], 301);
    }

    $password = Str::random(10);
    $user->password = bcrypt($password);
    $user->save();

    Mail::to($user->email)->send(new RecoverPassword($password));

    return response()->json([
      'status' => 'success'
    ], 200);
  }
}
