<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('titulo')->nullable();
            $table->text('ano')->nullable();
            $table->text('numero')->nullable();
            $table->text('descricao')->nullable();
            $table->integer('id_grupo')->unsigned()->nullable();
            $table->foreign('id_grupo')->references('id')->on('grupos');
            $table->integer('id_tipo_ato')->unsigned()->nullable();
            $table->foreign('id_tipo_ato')->references('id')->on('tipo_atos');
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
        Schema::dropIfExists('atos');
    }
}
