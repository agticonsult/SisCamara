<?php

use App\Models\TipoVotacao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoVotacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_votacaos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descricao')->nullable();
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(TipoVotacao::ATIVO);
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
        Schema::dropIfExists('tipo_votacaos');
    }
}
