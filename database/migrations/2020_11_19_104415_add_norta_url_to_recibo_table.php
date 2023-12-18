<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNortaUrlToReciboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recibo', function (Blueprint $table) {
          $table->string('nota_url', 90)->nullable()->after('factura_url');
        });
    }

    /**
     * Reverse the migrations.
	   *
     * @return void
     */
    public function down()
    {
        Schema::table('recibo', function (Blueprint $table) {
          $table->dropColumn('factura_url');
        });
    }
}
