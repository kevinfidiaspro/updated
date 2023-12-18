<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlbaranesTable extends Migration
{
    public function up()
    {
        Schema::create('albaranes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('proveedor_id');
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albaranes');
    }
}
