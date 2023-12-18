<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasDiariasTFG extends Model
{
    use HasFactory;
    protected $table = 'ventas_diarias_tfg';
    protected $fillable = ['dia','codigo','id_empresa','precio','presupuesto','gasto','pagado'];
    public function Empresa(){
        return $this->hasOne(EmpresaTFG::class,'id','id_empresa');
    }
    
}
