<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    use HasFactory;

    protected $table = "tickets";

    protected $fillable = [ 'id_estado_ticket','id_departamento','descripcion', 'id_urgencia', 'id_usuario', 'fecha_finalizacion', 'id_responsable' ];

    public function estadoTicket(){
        return $this->hasOne(EstadoTickets::class,'id','id_estado_ticket');
    }

    public function departamento(){
        return $this->hasOne(Departamento::class,'id','id_departamento');
    }

    public function user(){
        return $this->hasOne(User::class,'id','id_usuario');
    }

    public function responsable(){
        return $this->hasOne(User::class,'id','id_responsable');
    }

    public function urgencia(){
        return $this->hasOne(Urgencia::class,'id','id_urgencia');
    }
}
