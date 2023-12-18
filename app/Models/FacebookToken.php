<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookToken extends Model
{
    protected $table = 'facebook_token';
    use HasFactory;
    protected $fillable = [

                "nombre",
                    "token",
                    'page_id'


    ];
}
