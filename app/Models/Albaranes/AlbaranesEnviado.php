<?php

namespace App\Models\Albaranes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cliente;
use App\Models\AlbaranEnviadoItemAgregado;

class AlbaranesEnviado extends Model
{
    use HasFactory;

    protected $table = 'albaranes_enviados';

    protected $fillable = [
      'user_id',
      'cliente_id',
      'fecha',
      'descripcion',
      'cantidad',
      'precio',
      'importe',
      'nro_factura',
      'url',
      'json_recibido',
      'contabilizado',
    //   'cobrado',
    ];

     public function itemAlbaran(){
        return $this->hasMany(AlbaranEnviadoItemAgregado::class, 'albaran_enviado_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

}
