<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingTFG extends Model
{
    use HasFactory;
    protected $table = 'marketing_tfg';
    protected $fillable = ['mes','invertido','clics','id_web'];
    public function Web(){
        return $this->hasOne(EmpresaTFG::class,'id','id_web');
    }
    
}
