<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Provincia;
use App\Mail\ForgotPasswordEmail;
use Illuminate\Support\Facades\Mail;
use NotificationChannels\WebPush\HasPushSubscriptions;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasPushSubscriptions;

    protected $table = 'users';
    protected $appends = ['nombres'];
    protected $fillable = [
        'user_id',
        'nombre',
        'nombre_fiscal',
        'nombre_comercial',
        'cif',
        'telefono',
        'email',
        'direccion',
        'codigo_postal',
        'localidad',
        'provincia_id',
        'cuenta',
        'fecha_alta',
        'observaciones',
        'role',
        'avatar',
        'token_redes',
        'token_fichaje',
        'naf',
        'rol_tfg',
        'vendedor_id',
        'color'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->queue(new ForgotPasswordEmail($this->email, $token));
    }
    public function Vendedor(){
      return $this->hasOne(User::class,'id','vendedor_id');
    }
    public function Clientes(){
      return $this->hasMany(User::class,'vendedor_id','id');
    }
    public function Vacaciones(){
      return $this->hasMany(Vacaciones::class,'id_usuario','id');
    }
    public function provincia(){
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }
    public function getNombresAttribute(){
      return $this->nombre.' - '.$this->nombre_fiscal;
    }
    public function Proyectos(){
      return $this->hasMany(Proyecto::class,'usuario_id','id');
    }
    
    public function proyecto(){
    return $this->belongsTo(Proyecto::class);
    }
    public function proyectosMensuales(){
      return $this->hasMany(Proyecto::class,'usuario_id','id')->where('activo',1)->where('tipo_proyecto',2);
      }
    public function potencial(){
    return $this->belongsTo(Proyecto::class);
    }

    public function setPasswordAttribute($password){
       $this->attributes['password'] = $password;
    }

    public function chat(){
      return $this->hasOne(Chat::class);
    }

    public function fichaje(){
      return $this->hasMany(Fichar::class, 'usuario_id','id');
    }

    public function facturas(){
      return $this->hasManyThrough(Factura::class, Proyecto::class, 'usuario_id', 'id_proyecto', 'id', 'id')
        ->orderBy('created_at', 'DESC');
    }

}
