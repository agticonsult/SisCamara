<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioVotacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horario_votacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('horario')->nullable();
            $table->integer('id_tipo_horario')->unsigned()->nullable();
            $table->foreign('id_tipo_horario')->references('id')->on('tipo_horario_votacaos');
            $table->bigInteger('id_votacao')->unsigned()->nullable();
            $table->foreign('id_votacao')->references('id')->on('votacao_eletronicas');
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
        Schema::dropIfExists('horario_votacaos');
    }
}
