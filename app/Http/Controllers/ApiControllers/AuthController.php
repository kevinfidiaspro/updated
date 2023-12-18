<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResourceApp;
use DB;
use App\Models\RolesTFG;
class AuthController extends Controller
{
  public function getRolesTFG(Request $request){
    return RolesTFG::all();
  }
  public function login(Request $request){
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => ['These credentials do not match our records.']], 401);
    }
    $token = $user->createToken('my-app-token')->plainTextToken;
    $response = ['user' => $user, 'token' => $token];
    return response()->json($response, 200);
  }

  public function loginApp(Request $request){
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => ['These credentials do not match our records.']], 401);
    }
    $token = $user->createToken('my-app-token')->plainTextToken;

    $usuario = User::where('email', $request->email)->get();
    $userApp = UserResourceApp::collection($usuario);

    $response = ['user' => $userApp, 'token' => $token];
    if($request -> fcm_token != null){
      app('App\Http\Controllers\ApiControllers\FCMNotificationController')->StoreToken($user->id,$request->fcm_token);

    }
    return response()->json($response, 201);
  }

  public function logout(Request $request){
    if($request->user()){
      return $request->user()->tokens()->delete();
    }
    return response()->json('logout', 200);
  }
}
