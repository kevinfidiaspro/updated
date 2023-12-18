<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteTable extends Migration
{
    public function up(){
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('dni', 30);
            $table->string('email', 60)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->text('direccion')->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->integer('provincia_id')->nullable();
            $table->string('localidad', 45)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('cliente');
    }
}
