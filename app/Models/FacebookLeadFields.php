<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookLeadFields extends Model
{
    protected $table = 'facebook_lead_fields';
    use HasFactory;
    protected $fillable = ["lead_id","name","value"];
}