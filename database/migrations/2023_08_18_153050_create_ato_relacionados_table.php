<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtoRelacionadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ato_relacionados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_ato_principal')->unsigned()->nullable();
            $table->foreign('id_ato_principal')->references('id')->on('atos');
            $table->bigInteger('id_ato_relacionado')->unsigned()->nullable();
            $table->foreign('id_ato_relacionado')->references('id')->on('atos');
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
        Schema::dropIfExists('ato_relacionados');
    }
}
