<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Password;

class PasswordController extends Controller
{

  public function getAllPasswords(){
    $passwords = Password::get();
    return response()->json($passwords, 200);
  }
  public function deletePassword($password_id){
    $passwords = Password::find($password_id);
    $passwords->delete();
    return response()->json($passwords, 200);
  }
  public function getPasswordByid($password_id){
    $password = Password::find($password_id);
    return response()->json($password, 200);
  }
  public function savePassword(Request $request){ 
    $password = Password::updateOrCreate(['id' => $request->id], $request->all());
    return response()->json($password, 200);
  }
}
