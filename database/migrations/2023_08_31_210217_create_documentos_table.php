<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome');
            $table->string('assunto')->nullable();
            $table->text('conteudo')->nullable();
            $table->integer('id_localizacao')->unsigned()->nullable();
            $table->foreign('id_localizacao')->references('id')->on('localizacao_documentos');
            $table->integer('id_status')->unsigned()->nullable();
            $table->foreign('id_status')->references('id')->on('status_documentos');
            $table->bigInteger('id_modelo')->unsigned()->nullable();
            $table->foreign('id_modelo')->references('id')->on('modelo_documentos');
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
        Schema::dropIfExists('documentos');
    }
}
