<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembroGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membro_grupos', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Grupo
            $table->integer('id_grupo')->unsigned();
            $table->foreign('id_grupo')->references('id')->on('grupos');

            // Usuário
            $table->uuid('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users');

            // É administrador do grupo?
            $table->boolean('adm')->nullable();

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
        Schema::dropIfExists('membro_grupos');
    }
}
