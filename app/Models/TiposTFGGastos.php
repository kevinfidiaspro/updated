<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposTFGGastos extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'tipos_tfg_gastos';
    protected $fillable = ['nombre'];
}
