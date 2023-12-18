<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGestorTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestor_tareas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("status_id");
            $table->string("titulo", 100);
            $table->text("descripcion")->nullable();
            $table->date("fecha_creacion");
            $table->date("fecha_finalizacion")->nullable();
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('status_tareas')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gestor_tareas');
    }
}
