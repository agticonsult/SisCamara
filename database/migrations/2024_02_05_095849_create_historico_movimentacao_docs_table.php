<?php

use App\Models\HistoricoMovimentacaoDoc;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoMovimentacaoDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico_movimentacao_docs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('dataEncaminhado')->nullable();
            $table->date('dataAprovado')->nullable();
            $table->date('dataReprovado')->nullable();
            $table->uuid('aprovadoPor')->nullable();
            $table->foreign('aprovadoPor')->references('id')->on('users');
            $table->uuid('reprovadoPor')->nullable();
            $table->foreign('reprovadoPor')->references('id')->on('users');
            $table->integer('id_status')->unsigned()->nullable();
            $table->foreign('id_status')->references('id')->on('status_departamento_documentos');
            // $table->integer('id_departamento_encaminhado')->unsigned()->nullable();
            // $table->foreign('id_departamento_encaminhado')->references('id')->on('departamentos');
            $table->integer('id_documento')->unsigned()->nullable();
            $table->foreign('id_documento')->references('id')->on('departamento_documentos');
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(HistoricoMovimentacaoDoc::ATIVO);
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
        Schema::dropIfExists('historico_movimentacao_docs');
    }
}
