<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinhaAtosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linha_atos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ordem')->nullable();
            $table->integer('sub_ordem')->nullable()->default(0);
            $table->text('texto')->nullable();
            $table->boolean('alterado')->nullable();
            $table->bigInteger('id_ato_principal')->unsigned()->nullable();
            $table->foreign('id_ato_principal')->references('id')->on('atos');
            $table->bigInteger('id_ato_add')->unsigned()->nullable();
            $table->foreign('id_ato_add')->references('id')->on('atos');
            $table->integer('id_tipo_linha')->unsigned()->nullable();
            $table->foreign('id_tipo_linha')->references('id')->on('tipo_linha_atos');
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->nullable();
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
        Schema::dropIfExists('linha_atos');
    }
}
