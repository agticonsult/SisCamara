<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo')->nullable();
            $table->integer('ano')->nullable();
            $table->string('numero')->nullable();
            $table->text('subtitulo')->nullable();
            $table->date('data_publicacao')->nullable();
            $table->boolean('altera_dispositivo')->nullable();
            $table->integer('id_classificacao')->unsigned()->nullable();
            $table->foreign('id_classificacao')->references('id')->on('classificacao_atos');
            $table->integer('id_forma_publicacao')->unsigned()->nullable();
            $table->foreign('id_forma_publicacao')->references('id')->on('forma_publicacao_atos');
            $table->integer('id_orgao')->unsigned()->nullable();
            $table->foreign('id_orgao')->references('id')->on('orgao_atos');
            $table->integer('id_assunto')->unsigned()->nullable();
            $table->foreign('id_assunto')->references('id')->on('assunto_atos');
            $table->integer('id_grupo')->unsigned()->nullable();
            $table->foreign('id_grupo')->references('id')->on('grupos');
            $table->integer('id_tipo_ato')->unsigned()->nullable();
            $table->foreign('id_tipo_ato')->references('id')->on('tipo_atos');
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
        Schema::dropIfExists('atos');
    }
}
