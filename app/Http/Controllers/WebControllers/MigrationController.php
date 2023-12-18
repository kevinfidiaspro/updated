<?php

namespace App\Http\Controllers;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

class MigrationController extends Controller
{
    public function crear_tabla_chat(){
     Schema::create('chat', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->unsignedBigInteger('user_id');
         $table->foreign('user_id')->references('id')->on('users');
         $table->timestamps();
     });
     return ['status' => 200];
   }

   public function crear_tabla_mensajes(){
    Schema::create('chat_mensaje', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->integer('chat_id');
        $table->text('mensaje');
        $table->unsignedBigInteger('from_user')->nullable()->default(NULL);
        $table->unsignedBigInteger('to_user')->nullable()->default(NULL);
        $table->boolean('visto')->default(0);
        $table->foreign('from_user')->references('id')->on('users');
        $table->foreign('to_user')->references('id')->on('users');
        $table->timestamps();
    });
    return ['status' => 200];
  }


  public function add_device_token(){
   Schema::table('users', function (Blueprint $table) {
       $table->text('device_token')->nullable()->default(NULL)->after('remember_token');
   });
   return ['status' => 200];
 }

 public function tansform_to_polimorfic(){
    Schema::create('archivos', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->integer('archivos_id');
        $table->string('archivos_type');
        $table->string('file_name');
        $table->string('url');
        $table->timestamps();
        $table->SoftDeletes();
    });
    return ['status' => 200];
  }
}
