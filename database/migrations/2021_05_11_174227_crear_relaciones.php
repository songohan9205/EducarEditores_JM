<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearRelaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empleado', function (Blueprint $table) {
            $table->bigInteger('cargo_id')->unsigned()->after('id');
            $table->foreign('cargo_id')->references('id')->on('cargo');
        });

        Schema::table('recarga', function (Blueprint $table) {
            $table->bigInteger('empleado_id')->unsigned()->after('id');
            $table->foreign('empleado_id')->references('id')->on('empleado');
        });

        Schema::table('transferencia', function (Blueprint $table) {
            $table->bigInteger('empleado_recibe')->unsigned()->after('id');
            $table->foreign('empleado_recibe')->references('id')->on('empleado');
        });

        Schema::table('transferencia', function (Blueprint $table) {
            $table->bigInteger('empleado_transfiere')->unsigned()->after('id');
            $table->foreign('empleado_transfiere')->references('id')->on('empleado');
        });

        Schema::table('gasto', function (Blueprint $table) {
            $table->bigInteger('empleado_id')->unsigned()->after('id');
            $table->foreign('empleado_id')->references('id')->on('empleado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        Schema::table('empleado', function (Blueprint $table) {
            $table->dropForeign('empleado_cargo_id_foreign');
        });

        Schema::table('recarga', function (Blueprint $table) {
            $table->dropForeign('recarga_empleado_id_foreign');
        });

        Schema::table('transferencia', function (Blueprint $table) {
            $table->dropForeign('transferencia_empleado_recibe_foreign');
        });

        Schema::table('transferencia', function (Blueprint $table) {
            $table->dropForeign('transferencia_empleado_transfiere_foreign');
        });

        Schema::table('gasto', function (Blueprint $table) {
            $table->dropForeign('gasto_empleado_id_foreign');
        });
    }
}
