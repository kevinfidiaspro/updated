<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTareaPorDraggsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarea_por_draggs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("drag_id");
            $table->unsignedBigInteger("tarea_id");
            $table->timestamps();

             $table->foreign('tarea_id')->references('id')->on('gestor_tareas')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreign('drag_id')->references('id')->on('draggable_lists')
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
        Schema::dropIfExists('tarea_por_draggs');
    }
}
