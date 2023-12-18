<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookForm extends Model
{
    public $incrementing = false; 
    protected $table = 'facebook_form';
    use HasFactory;
    protected $fillable = ["id","page_id","locale","name","status",'id_servicio','id_page_sendinblue','field_email','field_name','field_phone'];
    protected $appends = ["page_id"];
    public function Servicio(){
        return $this-> hasOne(Servicio::class,'id','id_servicio');
    }
    public function Leads(){
        return $this->hasMany(FacebookLead::class,'form_id','id');
    }
    public function getPageIdAttribute(){
        $result = $this->Leads()->first();
        if($result != null){
            return $result->page_id;
        }else{
            return null;
        }
    }
}