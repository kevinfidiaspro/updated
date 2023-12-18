<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasKitDigital extends Model
{
    use HasFactory;
    protected $table='ventas_kit_digital';
    protected $fillable = ['bono_digital','pagado','iva','id_estado','num_acuerdo','importe','id_cliente','id_categoria','fecha_firma','fecha_iva','justificacion_1','cobro_1','justificacion_2','cobro_2'];
    public function Cliente(){
        return $this->hasOne(User::class,'id','id_cliente');
    }
    public function Estado(){
        return $this->hasOne(EstadoVentasKit::class,'id','id_estado');
    }
    public function Categoria(){
        return $this->hasOne(CategoriaKitDigital::class,'id','id_categoria');
    }
}
