<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class NroNota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nro_nota';

    protected $fillable = ['recibo_id', 'nro_nota',  'user_id'];

    protected $casts = ['created_at'  => 'date:Y-m-d'];

    public function recibo(){
      return $this->belongsTo(Recibo::class);
    }

    public function deuda() {
       return $this->morphOne(Deuda::class, 'deuda');
    }  
}
