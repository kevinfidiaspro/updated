<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReciboServicio extends Model
{
    use HasFactory;

    protected $table = 'recibo_servicios';

    protected $fillable = [
      'recibo_id',
      'descripcion',
      'cantidad',
      'precio',
      'importe',
      'user_id'
    ];

    public function recibo(){
      return $this->belongsTo(Recibo::class, 'recibo_id');
    }
}
