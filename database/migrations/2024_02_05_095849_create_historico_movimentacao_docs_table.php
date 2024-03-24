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
            $table->text('parecer')->nullable();
            $table->integer('id_documento')->unsigned()->nullable();
            $table->foreign('id_documento')->references('id')->on('documentos');
            $table->uuid('id_usuario')->nullable();
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->integer('id_status')->unsigned()->nullable();
            $table->foreign('id_status')->references('id')->on('status_documentos');
            $table->integer('id_departamento')->unsigned()->nullable();
            $table->foreign('id_departamento')->references('id')->on('departamentos');
            $table->integer('atualDepartamento')->nullable();
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
