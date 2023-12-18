<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookPage extends Model
{
    protected $table = 'facebook_page';
    use HasFactory;
    protected $appends = ['token'];
    protected $fillable = [       
        "id",
        "name",
        "sendinblue_key",
    ];
    public function FacebookToken(){
        return $this->hasOne(FacebookToken::class,'page_id','id');
    }
    public function getTokenAttribute(){
        $token = $this->FacebookToken;
        if($token == null){
            return null;
        }
        else{
            return $token ->token;
        }
    }
}
