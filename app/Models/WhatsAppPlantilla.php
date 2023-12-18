<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppPlantilla extends Model
{
    use HasFactory;
    protected $table = "whatsapp_plantilla";
    protected $fillable = ['nombre'];

}
