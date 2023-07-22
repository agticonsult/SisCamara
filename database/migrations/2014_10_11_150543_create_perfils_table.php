<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerfilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfils', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descricao');
            $table->integer('id_abrangencia')->unsigned()->nullable();
            $table->foreign('id_abrangencia')->references('id')->on('abrangencias');
            $table->integer('id_tipo_perfil')->unsigned()->nullable();
            $table->foreign('id_tipo_perfil')->references('id')->on('tipo_perfils');
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->uuid('inativadoPorUsuario')->nullable();
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
        Schema::dropIfExists('perfils');
    }
}
