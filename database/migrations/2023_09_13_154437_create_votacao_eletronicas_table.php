<?php

use App\Models\VotacaoEletronica;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotacaoEletronicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votacao_eletronicas', function (Blueprint $table) {
            $table->increments('id');
            $table->date('data')->nullable();
            $table->integer('interrupcoes')->nullable();
            $table->boolean('votacaoIniciada')->nullable();
            $table->boolean('votacaoPausada')->nullable();
            $table->boolean('votacaoEncerrada')->nullable();
            $table->timestamp('dataHoraInicio')->nullable();
            $table->timestamp('dataHoraFim')->nullable();
            $table->integer('id_tipo_votacao')->unsigned()->nullable();
            $table->foreign('id_tipo_votacao')->references('id')->on('tipo_votacaos');
            $table->bigInteger('id_proposicao')->unsigned()->nullable();
            $table->foreign('id_proposicao')->references('id')->on('proposicaos');
            $table->integer('id_legislatura')->unsigned()->nullable();
            $table->foreign('id_legislatura')->references('id')->on('legislaturas');
            $table->integer('id_status_votacao')->unsigned()->nullable();
            $table->foreign('id_status_votacao')->references('id')->on('status_votacaos');
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(VotacaoEletronica::ATIVO);
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
        Schema::dropIfExists('votacao_eletronicas');
    }
}
