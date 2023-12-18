<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocionTable extends Migration
{
    public function up(){
        Schema::create('promocion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
			$table->string('file_name', 120);
			$table->string('url', 120)->nullable();
			$table->text('usuarios');
            $table->boolean('activo');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(){
        Schema::dropIfExists('promocion');
    }
}
