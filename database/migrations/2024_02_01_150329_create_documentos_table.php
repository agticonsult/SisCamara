<?php

use App\Models\Documento;
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
            $table->increments('id');
            $table->string('titulo')->nullable();
            $table->text('conteudo')->nullable();
            $table->text('protocolo')->nullable();
            $table->integer('id_tipo_documento')->unsigned()->nullable();
            $table->foreign('id_tipo_documento')->references('id')->on('tipo_documentos');
            $table->integer('id_tipo_workflow')->unsigned()->nullable();
            $table->foreign('id_tipo_workflow')->references('id')->on('tipo_workflows');
            $table->boolean('reprovado_em_tramitacao')->default(false);
            $table->boolean('finalizado')->default(false);
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(Documento::ATIVO);
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
