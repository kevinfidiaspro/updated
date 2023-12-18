<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reunion;
use Notification;
use App\Models\ProyectoTarea;
use App\Notifications\ReunionNotification;
class PushController extends Controller
{
    public function Subscribe(Request $request){
        $user= $request->user();
        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        $user->updatePushSubscription($request->endpoint, $key, $token,null);
    }
    public function sendTest(Request $request){
        Notification::send(User::all(),new ReunionNotification('[rueba','prueba'));
        return 'asd';
    }
    public function checkTime(Request $request){
        $current_date = date('Y-m-d');
        // Get current time in format HH:MM:SS
        $hour = date('H');
        $minute = date('i');
        if($minute > 15 && $minute < 45){
            $hour += 0.5;
        }
        if($minute >= 45){
            $hour += 1;
        }
        $previous_hour = $hour+0.5;
        $reuniones_media_hora = Reunion::where('hora_desde',$previous_hour)->where('fecha',$current_date)->get();
        $reuniones = Reunion::where('hora_desde',$hour)->where('fecha',$current_date)->get();
        foreach($reuniones_media_hora as $reunion){
            foreach($reunion->Asistentes as $asistente){
                Notification::send($asistente->Asistente,new ReunionNotification($reunion,'Tiene una Reunion en media hora'));

            }
        }
        foreach($reuniones as $reunion){
            foreach($reunion->Asistentes as $asistente){
                Notification::send($asistente->Asistente,new ReunionNotification($reunion,'Tiene una Reunion en este momento'));

            }
        }
    }
    public function checkTimeSeguimientos(Request $request){
        $current_date = date('Y-m-d');
        // Get current time in format HH:MM:SS
        $hour = date('H');
        $minute = date('i');
        if($minute > 15 && $minute < 45){
            $hour += 0.5;
        }
        if($minute >= 45){
            $hour += 1;
        }
        $previous_hour = $hour+0.5;
        $reuniones_media_hora = ProyectoTarea::where('hora',$previous_hour)->where('fecha',$current_date)->where('alarma',1)->get();
        $reuniones = ProyectoTarea::where('hora',$hour)->where('fecha',$current_date)->where('alarma',1)->get();
        $users = User::where('role',5)->get();
        foreach($reuniones_media_hora as $reunion){
            Notification::send($users,new ReunionNotification($reunion,'Tiene una seguimiento en media hora'));
        }
        foreach($reuniones as $reunion){
            Notification::send($users,new ReunionNotification($reunion,'Tiene un seguimiento en este momento'));
        }
        
    }
}
