<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdUsuarioToTablesTable extends Migration
{
    protected $tables = ['albaranes',
    'cliente', 
    'nro_factura', 
    'nro_nota',
    'nro_parte_trabajo',
    'nro_presupuesto',
    'proveedores', 
    'recibo',
    'recibo_servicios'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $key => $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->after('id');

                $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
}
