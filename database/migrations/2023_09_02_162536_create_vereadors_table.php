<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVereadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vereadors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('dataInicioMandato')->nullable();
            $table->date('dataFimMandato')->nullable();
            $table->integer('id_cargo_eletivo')->unsigned()->nullable();
            $table->foreign('id_cargo_eletivo')->references('id')->on('cargo_eletivos');
            $table->integer('id_pleito_eleitoral')->unsigned()->nullable();
            $table->foreign('id_pleito_eleitoral')->references('id')->on('pleito_eleitorals');
            $table->uuid('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
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
        Schema::dropIfExists('vereadors');
    }
}
