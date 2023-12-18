<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes; 

    protected $table = 'proveedores';

    protected $fillable = [
      'nombre',
      'email',
      'telefono',
      'user_id'
    ];

    protected $dates = ['created_at'];

    protected $casts = [
      'created_at' => 'date:d-m-Y'
    ];

    public function albaranes(){
      return $this->hasMany(Albaran::class);
    }
}
