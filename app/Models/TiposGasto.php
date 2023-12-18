<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TiposGasto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_gasto';

    protected $fillable = [
      'nombre',
      'user_id'
    ];

    protected $dates = ['created_at'];

    protected $casts = [
      'created_at' => 'date:d-m-Y'
    ];

    public function albaranes(){
      return $this->hasMany(Gasto::class);
    }

    public function gasto()
    {
        return $this->belongsTo(Gasto::class);
    }
}
