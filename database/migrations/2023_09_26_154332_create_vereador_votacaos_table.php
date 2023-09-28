<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVereadorVotacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vereador_votacaos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('ordem')->nullable();

            $table->boolean('votou')->nullable();
            $table->timestamp('votouEm')->nullable();

            // Vereador
            $table->integer('id_vereador')->unsigned()->nullable();
            $table->foreign('id_vereador')->references('id')->on('agente_politicos');

            // Votação
            $table->integer('id_votacao')->unsigned()->nullable();
            $table->foreign('id_votacao')->references('id')->on('votacao_eletronicas');

            // Votação autorizada
            $table->boolean('votacaoAutorizada')->nullable();
            $table->uuid('autorizadaPorUsuario')->nullable();
            $table->foreign('autorizadaPorUsuario')->references('id')->on('users');
            $table->timestamp('autorizadaEm')->nullable();

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
        Schema::dropIfExists('vereador_votacaos');
    }
}
