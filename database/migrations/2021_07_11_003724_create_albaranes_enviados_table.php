<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbaranesEnviadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albaranes_enviados', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->date('fecha')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('cantidad')->nullable();
            $table->decimal('precio', 10,2)->nullable();
            $table->decimal('importe', 10,2)->nullable();
            $table->string('nro_factura')->nullable();
            $table->longText('url')->nullable();
            $table->timestamps();


            $table->foreign('cliente_id')->references('id')->on('cliente')
            ->onUpdate('cascade')
            ->onDelete('cascade');
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
        Schema::dropIfExists('albaranes_enviados');
    }
}
