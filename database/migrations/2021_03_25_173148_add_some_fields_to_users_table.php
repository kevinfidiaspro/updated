<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('provincia_id')->after('id');

            $table->string('nombre_fiscal',255)->nullable()->after('name');
            $table->string('cif',9)->nullable()->after('nombre_fiscal');
            $table->integer('telefono')->nullable()->after('cif');//Colocar max:9 in Request
            $table->string('ciudad', 60)->nullable()->after('telefono');
            $table->text('direccion', 300)->nullable()->after('ciudad');

            $table->foreign('provincia_id')->references('id')->on('provincias')
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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
