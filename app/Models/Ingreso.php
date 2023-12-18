<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';

    protected $fillable = [
      'codigo',
      'importe',
      'descripcion',
      'created_at',
      'user_id',
      'proyecto_id',
      'cliente_id',
      'factura_id',
      'created_at'
    ];
    protected $appends = ['cliente_nombre'];
    protected $dates = ['created_at'];
    public function getClienteNombreAttribute(){
      if($this->cliente){
        return $this->cliente->nombre_fiscal?$this->cliente->nombre_fiscal:$this->cliente->nombre;
      }
      else if($this->proyecto){
        if($this->proyecto->usuario){
          return $this->proyecto->usuario->nombre_fiscal ?$this->proyecto->usuario->nombre_fiscal:$this->proyecto->usuario->nombre;
        }else{
          return $this->proyecto->nombre;
        }
      }
      return 'No Tiene';
    }
    public function setCodigoAttribute($codigo){
      $this->attributes['codigo'] = mb_strtoupper($codigo);
    }
    public function cliente() {
      return $this->belongsTo(User::class);
    }
    public function proyecto() {
      return $this->belongsTo(Proyecto::class);
    }
    public function facturas(){
      return $this->belongsTo(Factura::class, 'factura_id');
    }
}
