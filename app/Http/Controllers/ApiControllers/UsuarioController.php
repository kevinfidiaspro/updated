<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\User;
use App\Models\Proyecto;
use App\Mail\NewUserMail;
use App\Models\Provincia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\User\UpdateUser;
use App\Models\GestorDocumental;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Users\UserRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use Storage;
use App\Models\DraggableList;
use App\Traits\Files\HandlerFiles;
use App\Http\Resources\EmpleadoResource;
//use Dotenv\Validator;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{

  protected function pathServer(){
    $PATH = $_SERVER['DOCUMENT_ROOT'];
    $pathPublicOut = explode('public',$PATH);
    $res = $pathPublicOut[0];
    return $res;
  }
  public function getUsuariosEmpleados(){
    $rol_id= [1,3,5,6,7,8,9];
    $user = User::whereIn('role', $rol_id)->orderBy('created_at', 'DESC')->paginate(100);
    return response()->json([ 'status' => 200, 'message' => 'Ok', 'users' => $user]);
  }
  public function getUsuariosEmpleadosAll(Request $request){
    $rol_id= [1,3,5,6,7,8,9];
    $user = User::whereIn('role', $rol_id)->orderBy('created_at', 'DESC')->get();
    $user = EmpleadoResource::collection($user)->toArray($request);
    return response()->json(['users' => $user ]);
  }
  public function getUsuarios(){
    //return response()->json([ 'status' => 200, 'message' => 'Ok', 'users' => $this->getUsuariosEmpleadosAll()]);
    $user = User::orderBy('created_at', 'DESC')->get();
    $roles = ['','Administrador','Cliente','Empleado','Potencial','Administración','Marketing','Supervisor Marketing','Atención al cliente','Ventas'];

    foreach ($user as  $value) {
      $value['role'] =  $roles[$value->role];

    }
   
    return response()->json([ 'status' => 200, 'message' => 'Ok', 'users' => $user]);
  }
  public function getClientes(Request $request){
    $users = User::whereHas('Proyectos',function($query) use ($request){
      $query->where('estado_id','!=',3);
      if($request->activo != null){
        //$query->where('activo',$request->activo);
      }
    });
    $user= $request->user();
    if($user->role == 9  || $user->role == 8){

        $users->where('vendedor_id',$user->id);

    }else if($user->role != 1 && $user->role != 5 && $user->role != 7){
      $users->whereHas('Proyectos',function($query)use($request,$user){
        $query->where('activo',1)->whereHas('usuarios', function($q) use($user){
          $q->where('id_usuario',$user->id);
        });
      });
    }
    if($request->semana != null){
      $users->whereHas('Proyectos',function($query) use ($request){
        $query->where('semanal',$request->semana);
      });
    }
    if($request->servicio){
      $users->whereHas('Proyectos',function($query) use ($request){
        $query->where('servicio_id',$request->servicio);
      });
    }
    if($request->estado){
      $users->whereHas('Proyectos',function($query) use ($request){
        $query->whereHas('estadosProyecto',function($query) use ($request){
          $query->where('id_estado',$request->estado);
        });
      });
    }
    if($request->busqueda){
      $users->where(function($query)use($request){
        $query->where('nombre','LIKE','%'.$request->busqueda.'%');
        $query->orWhere('nombre_fiscal','LIKE','%'.$request->busqueda.'%');
        $query->orWhere('nombre_comercial','LIKE','%'.$request->busqueda.'%');
        $query->orWhere('email','LIKE','%'.$request->busqueda.'%');
        $query->orWhere('telefono','LIKE','%'.$request->busqueda.'%');
        $query->orWhere('fecha_alta','LIKE','%'.$request->busqueda.'%');
        $query->orWhereHas('Proyectos',function($query) use ($request){
          $query->where('nombre','LIKE','%'.$request->busqueda.'%');
        });
      });
    }
    $users = $users->orderBy('created_at', 'DESC');
    if($request->amount == -1){
      $data = $users->get();
      $result= ['total'=>count($data),'data'=>$data];
    }
    else{
      $result = $users->paginate($request->amount??15);
    }
    return response()->json([ 'status' => 200, 'message' => 'Ok', 'users' => $result]);
  }
  public function getAllClientes(){
    $users = User::where('role', 2)->with('Proyectos')->whereHas('Proyectos',function($query){
      $query->where('estado_id',2);
    })->orderBy('created_at', 'DESC')->get();
    return response()->json([ 'status' => 200, 'message' => 'Ok', 'users' => $users]);
  }
  
  public function getAllInactiveClientes(){
    $users = User::where('role', 2)->whereHas('Proyectos',function($query){
      $query->where('estado_id',2);
      $query->where('activo',0);
    })->orderBy('created_at', 'DESC')->get();
    return response()->json([ 'status' => 200, 'message' => 'Ok', 'users' => $users]);
  }
  public function getAllActiveClientes(){
    $users = User::where('role', 2)->whereHas('Proyectos',function($query){
      $query->where('estado_id',2);
      $query->where('activo',1);
    })->orderBy('created_at', 'DESC')->get();
    return response()->json([ 'status' => 200, 'message' => 'Ok', 'users' => $users]);
  }
  public function getUsuarioByid($user_id){
    $user = User::find($user_id);
    $provincias = Provincia::orderBy('nombre')->get(['id', 'nombre']);
    return response()->json([ 'status' => 200, 'message' => 'Usuario Actualizado', 'user' => $user, 'provincias' => $provincias ]);
  }
  public function saveUsuario(Request $request){

    /*$validate = $request->validate([
        'usuario.nombre' => 'required',
        'usuario.nombre_fiscal' => 'required',
        'usuario.cif' => 'required|max:9',
        'usuario.telefono' => 'required|max:9',
        'usuario.email' => 'required',
        'usuario.role' => 'required',
        'usuario.provincia_id' => 'required',
        'usuario.localidad' => 'required|max:60',
        'usuario.codigo_postal' => 'required|numeric',
    ]);*/

    /*if(array_key_exists("message", $validate)) {
        return response()->json([
            'error' => $validate
        ], 422);
    }*/

    $user_collet = collect(json_decode($request->usuario));
    $validate = Validator::make($user_collet->toArray(), [
        'nombre' => 'required',
        'nombre_fiscal' => 'required',
        'cif' => 'required|max:9',
        'telefono' => 'required|max:9',
        'email' => 'required',
        'role' => 'required',
        'provincia_id' => 'required',
        'localidad' => 'required|max:60',
        'codigo_postal' => 'required|numeric',
    ]);

    if ($validate->fails()) {
        return response()->json([
            'errors' => $validate->errors()
        ], 422);
    }

    $usuario = json_decode($request->usuario);

    if($usuario->id == 0 || $usuario->id == null ||  $usuario->id == '' ){
      $user = new User();
    }else{
      $user = User::where('id','=',$usuario->id)->first();
      return response()->json($user, 200);
    }
    $destination = $this->pathServer() . 'storage/app/public/users/userId_' . $user->id . '/';
    $store = HandlerFiles::store($request, $destination);
    $this->crearCarpetasPrinciales($user->id);

    $user->user_id = $usuario->user_id;
    $user->nombre = $usuario->nombre;
    $user->nombre_fiscal = $usuario->nombre_fiscal;
    $user->nombre_comercial = $usuario->nombre_comercial;

    $user->cif = $usuario->cif;
    $user->telefono = $usuario->telefono;
    $user->email = $usuario->email;
    $user->direccion = $usuario->direccion;
    $user->codigo_postal = $usuario->codigo_postal;
    $user->localidad = $usuario->localidad;
    $user->provincia_id = $usuario->provincia_id;
    $user->fecha_alta = $usuario->fecha_alta;
    $user->observaciones =  strlen($usuario->observaciones) > 0 ? $usuario->observaciones : '';
    $user->cuenta = $usuario->cuenta;
    $password = Str::random(10);
    $user->password  = Hash::make($password);
    $user->role = $usuario->role;
    $user->rol_tfg = isset($usuario->rol_tfg)?$usuario->rol_tfg:null;
    $user->color = $request->color;
    $user->naf = isset($usuario->naf)??'';

    $user->avatar = $usuario->avatar;
    $user->saveOrFail();

    $email = $user->email;
    //Se envía mail luego de crear el usuario
    //Mail::to($email)->queue(new NewUserMail($user, $password));
    return response()->json($user, 200);
  }
  public function updateUsuario(UserUpdateRequest $request, $id){

    $usuario = json_decode($request->usuario);

    if($request->isMethod("POST")){
      $user = User::findOrFail($id);
      $user->user_id = $usuario->user_id;
      $user->nombre = $usuario->nombre;
      $user->nombre_fiscal = $usuario->nombre_fiscal;
      $user->nombre_comercial = $usuario->nombre_comercial;
      $user->cif = $usuario->cif;
      $user->telefono = $usuario->telefono;
      $user->email = $usuario->email;
      $user->direccion = $usuario->direccion;
      $user->codigo_postal = $usuario->codigo_postal;
      $user->localidad = $usuario->localidad;
      $user->provincia_id = $usuario->provincia_id;
      $user->fecha_alta = $usuario->fecha_alta;
      $user->observaciones = strlen($usuario->observaciones) > 0 ? $usuario->observaciones : '';
      $user->cuenta = $usuario->cuenta;
      $user->role = $usuario->role;
      $user->rol_tfg = $usuario->rol_tfg;
      $user->color = $usuario->color;
      $user->avatar = $usuario->avatar;
      $user->naf = $usuario->naf;

      $user->update();

      $destination = $this->pathServer() . '/laravel/storage/app/public/users/userId_' . $user->id . '/';
      $store = HandlerFiles::store($request,  $destination);

      //update user mail
      $email = $usuario->email;
      if (!isset($request->existeDatosEmpres)) {
        return 1;
        Mail::to($email)->queue(new UpdateUser($user));
      }
      return response()->json([ 'status' => 200, 'message' => 'Usuario Actualizado', 'user' => $user ]);
    }else{
      return response()->json([ 'status' => 400, 'message' => 'Método no permitido', ]);
    }
  }
  public function deleteUsuario($user_id){
    $user = User::find($user_id);
    $user->delete();
    return response()->json($user, 200);
  }
  //datos que se necesitaran en el formulario
  public function getMethodsForm(){
    $provincias = Provincia::orderBy('nombre')->get(['id', 'nombre']);
    return response()->json([ 'status' => 200, 'message' => 'Ok', 'provincias' => $provincias ]);
  }
  public function crearCarpetasPrinciales($user_id){
      $principal = ["documentacion_general", "factura", "factura_recibidas"];
      foreach ($principal as $value) {
        Storage::makeDirectory('public/documentos/userId_'.$user_id .'/' . $value);
      }
        Storage::makeDirectory('public/users/userId_'.$user_id);
  }
  public function crearDragPrincipales($user_id){
    $status = ['Pendientes', 'En Progreso', 'Finalizadas'];
    foreach ($status as $value) {
        $gD = new  DraggableList;
        $gD->user_id = $user_id;
        $gD->drag = $value;
        $gD->newTask = false;
        $gD->save();
    }
  }

  public function getUsersByAttribute($attribute){
    try{
      $user = User::select('id', 'telefono', 'email', 'nombre', 'nombre_fiscal')->where('telefono', $attribute)->orWhere('email', $attribute)->first();
      return response()->json(['success'=> $user, 200 ]);
    }catch(\Exception $e){
      return response()->json(['error' => $e->getMessage(), 400]);
    }

  }
  // public function getAllUsuarios(){
  //   $usuarios = UserResource::collection(Usuario::orderBy('nombre', 'ASC')->get());
  //   return response()->json($usuarios, 200);
  // }
  // public function getUsuarios($user_id){
  //   $usuarios = UserResource::collection(Usuario::orderBy('created_at', 'DESC')->where('user_id', '=', $user_id)->get());
  //   return response()->json($usuarios, 200);
  // }
  // public function getUsuarioByid($usuario_id){
  //   $usuario = Usuario::find($usuario_id);
  //   return response()->json($usuario, 200);
  // }
  // public function saveUsuario(UserRequest $request){
  //   $usuario = Usuario::updateOrCreate(['id' => $request->id], $request->all());
  //   return response()->json($usuario, 200);
  // }
  // public function deleteUsuario($usuario_id){
  //   $usuario = Usuario::find($usuario_id);
  //   $usuario->delete();
  //   return response()->json($usuario, 200);
  // }
}
