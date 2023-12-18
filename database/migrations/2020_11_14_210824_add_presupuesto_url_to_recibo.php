<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPresupuestoUrlToRecibo extends Migration
{
    public function up()
    {
        Schema::table('recibo', function (Blueprint $table) {
            $table->string('presupuesto_url', 90)->nullable()->after('total');
        });
    }

    public function down()
    {
        Schema::table('recibo', function (Blueprint $table) {
            $table->dropColumn('presupuesto_url');
        });
    }
}
