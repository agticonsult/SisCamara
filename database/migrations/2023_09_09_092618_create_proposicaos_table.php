<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposicaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposicaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo')->nullable();
            $table->string('assunto')->nullable();
            $table->text('conteudo')->nullable();
            $table->integer('id_localizacao')->unsigned()->nullable();
            $table->foreign('id_localizacao')->references('id')->on('localizacao_proposicaos');
            $table->integer('id_status')->unsigned()->nullable();
            $table->foreign('id_status')->references('id')->on('status_proposicaos');
            $table->bigInteger('id_modelo')->unsigned()->nullable();
            $table->foreign('id_modelo')->references('id')->on('modelo_proposicaos');
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
        Schema::dropIfExists('proposicaos');
    }
}
