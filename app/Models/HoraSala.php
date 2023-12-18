<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoraSala extends Model
{
    use HasFactory;

    protected $table = 'horas_sala';

    protected $fillable = [
      'hora',
    ];

    protected $dates = ['created_at'];

    protected $casts = [
      'created_at' => 'date:d-m-Y'
    ];

    public function reservaDesde() {
        return $this->hasMany(ReservaSala::class, 'desde', 'id');
    }

    public function reservaHasta() {
        return $this->belongsTo(ReservaSala::class, 'hasta', 'id');
    }
}
