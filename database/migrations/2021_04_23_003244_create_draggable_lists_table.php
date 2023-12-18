<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDraggableListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draggable_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            
            $table->string('drag')->comment('Nombre del Drag o se puede tomar como un status de la tarea');
           
            $table->boolean('newTask')->comment('true:mostrar card para crear nueva tarea false:cerrar card');
           
            $table->timestamps();

            
            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('draggable_lists');
    }
}
