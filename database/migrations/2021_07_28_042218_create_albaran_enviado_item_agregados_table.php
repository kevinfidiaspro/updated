<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbaranEnviadoItemAgregadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albaran_enviado_item_agregados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('albaran_enviado_id')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('cantidad')->nullable();
            $table->decimal('precio', 10,2)->nullable();
            $table->decimal('importe', 10,2)->nullable();
            $table->timestamps();



             $table->foreign('albaran_enviado_id')->references('id')->on('albaranes_enviados')
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
        Schema::dropIfExists('albaran_enviado_item_agregados');
    }
}
