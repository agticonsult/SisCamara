<?php

use App\Models\AnexoHistoricoMovimentacao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnexoHistoricoMovimentacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anexo_historico_movimentacaos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('nome_original');
            $table->text('nome_hash');
            $table->string('diretorio')->nullable();
            $table->integer('id_movimentacao')->unsigned()->nullable();
            $table->foreign('id_movimentacao')->references('id')->on('historico_movimentacao_docs');
            $table->boolean('ativo')->default(AnexoHistoricoMovimentacao::ATIVO);
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
        Schema::dropIfExists('anexo_historico_movimentacaos');
    }
}
