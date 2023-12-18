<?php
namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
class AuthenticationController extends Controller{
 
    public function recoverPasswordView(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    } 
    function recoverPassword(Request $request){
        $validator = Validator::make($request->all(), ['email' => 'required|email']);

        if ($validator->fails()){
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false,  'message'=>'Debe insertar un email valido'], 400);
        }
        $user = User::where('email',$request->email)->first();
        if($user){
            $status = Password::sendResetLink(
                $request->only('email')
            );
    
            return $status == Password::RESET_LINK_SENT
                        ? back()->with('status', __($status))
                        : back()->withInput($request->only('email'))
                                ->withErrors(['email' => __($status)]);
        }else{
            return response()->json(['status' => false,  'message'=>'Email no encontrado'], 400);

        }
    }
    public function UpdatePassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', [
                'reset' => '¡Su contraseña ha sido restablecida!',
                'sent' => '¡Recordatorio de contraseña enviado!',
                'token' => 'Este token de restablecimiento de contraseña es inválido.',
                'user' => 'No se ha encontrado un usuario con esa dirección de correo.',
                'throttled' => 'Por favor espere antes de volver a intentarlo.',
                'password' => 'Las contraseñas deben tener al menos seis caracteres y coincidir con la confirmación.'
            ]],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
//'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return response($status) ;
    }
}


