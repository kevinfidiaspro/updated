<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookLead extends Model
{
    protected $table = 'facebook_lead';
    use HasFactory;
    protected $fillable = [

               
                    "form_id",
        "leadgen_id",
   
        "page_id",

    ];
    protected $appends = ['value'];
    public function Form(){
        return $this->hasOne(FacebookForm::class,'id','form_id');
    }
    public function Token(){
        return $this->hasOne(FacebookToken::class,'page_id','page_id');
    }
    public function leadData(){
        return $this->hasMany(FacebookLeadFields::class,'lead_id','leadgen_id');
    }
    public function getValueAttribute(){
        $fields =[];
        $field_temp = [];
        foreach($this->leadData()->get() as $lead){
            $fields[$lead->name] = $lead->value;
        }
        return $fields;
    }

}
