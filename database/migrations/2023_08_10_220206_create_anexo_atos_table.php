<?php

use App\Models\AnexoAto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnexoAtosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anexo_atos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('nome_original');
            $table->text('nome_hash');
            $table->string('diretorio')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ehAudio')->nullable();
            $table->bigInteger('id_ato')->unsigned()->nullable();
            $table->foreign('id_ato')->references('id')->on('atos');
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(AnexoAto::ATIVO);
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
        Schema::dropIfExists('anexo_atos');
    }
}
