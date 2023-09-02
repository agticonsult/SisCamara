<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMandatosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mandatos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ano_inicio')->nullable();
            $table->integer('ano_fim')->nullable();
            $table->integer('id_pleito')->unsigned()->nullable();
            $table->foreign('id_pleito')->references('id')->on('pleito_eleitorals');
            $table->integer('id_cargo')->unsigned()->nullable();
            $table->foreign('id_cargo')->references('id')->on('cargo_eletivos');
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
        Schema::dropIfExists('mandatos');
    }
}
