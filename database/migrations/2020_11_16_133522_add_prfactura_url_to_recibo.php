<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrfacturaUrlToRecibo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recibo', function (Blueprint $table) {
          $table->string('factura_url', 90)->nullable()->after('presupuesto_url');
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
