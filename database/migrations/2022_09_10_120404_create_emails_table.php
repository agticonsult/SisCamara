<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('enviadoEm')->nullable();
            $table->timestamp('expiradoEm')->nullable();
            $table->integer('expirarMin')->nullable();
            $table->integer('expirarHora')->nullable();
            $table->text('link')->nullable();
            $table->text('emailRecebido')->nullable();
            $table->text('emailEnviado')->nullable();
            $table->boolean('expirado')->nullable();
            $table->uuid('recebidoPorUsuario')->nullable();
            $table->foreign('recebidoPorUsuario')->references('id')->on('users');
            $table->integer('id_tipo_email')->unsigned();
            $table->foreign('id_tipo_email')->references('id')->on('tipo_emails');
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
        Schema::dropIfExists('emails');
    }
}
