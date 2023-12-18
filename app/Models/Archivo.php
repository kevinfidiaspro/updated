<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'archivos';

    //protected $timestamp = 'false';

    protected $fillable = [
        'id_proyecto',
        'file_name',
        'url',
    ];

    protected $casts = [
      'created_at' => 'date:d-m-Y H:m:i'
    ]; 

    public function proyecto(){
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
    }

    public function archivos(){
      return $this->morphTo();
    }
}
