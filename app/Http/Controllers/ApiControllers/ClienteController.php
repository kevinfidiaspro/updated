<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ClienteRequest;
use App\Http\Resources\ClienteResource;

class ClienteController extends Controller
{
  public function getAllClientes(){
    $clientes = ClienteResource::collection(Cliente::orderBy('nombre', 'ASC')->get());
    return response()->json($clientes, 200);
  }

  public function getClientes($user_id){
    $clientes = ClienteResource::collection(Cliente::orderBy('created_at', 'DESC')->where('user_id', '=', $user_id)->get());
    return response()->json($clientes, 200);
  }

  public function getClienteByid($cliente_id){
    $cliente = Cliente::find($cliente_id);
    return response()->json($cliente, 200);
  }

  public function saveCliente(ClienteRequest $request){
    $cliente = Cliente::updateOrCreate(['id' => $request->id], $request->all());
    return response()->json($cliente, 200);
  }

  public function deleteCliente($cliente_id){
    $cliente = Cliente::find($cliente_id);
    $cliente->delete();
    return response()->json($cliente, 200);
  }
}
