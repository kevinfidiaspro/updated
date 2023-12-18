<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNroParteTrabajoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nro_parte_trabajo', function (Blueprint $table) {
          $table->id();
          $table->integer('nro_presupuesto_id')->nullable();
          $table->integer('recibo_id');
          $table->integer('nro_parte_trabajo');
          $table->timestamps();
          $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nro_parte_trabajo');
    }
}
