<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ReservaSala extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservas_sala';

    protected $fillable = [
      'desde',
      'hasta',
      'motivo',
      'user_id',
      'fecha',
    ];

    protected $dates = ['created_at'];

    protected $casts = [
      'created_at' => 'date:d-m-Y'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function horaDesde() {
        return $this->belongsTo(HoraSala::class, 'desde', 'id');
    }

    public function horaHasta() {
        return $this->belongsTo(HoraSala::class, 'hasta', 'id');
    }
}
