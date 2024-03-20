<?php

use App\Models\AuxiliarDocumentoDepartamento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuxiliarDocumentoDepartamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxiliar_documento_departamentos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_documento')->unsigned();
            $table->foreign('id_documento')->references('id')->on('departamento_documentos');
            $table->integer('id_departamento')->unsigned();
            $table->foreign('id_departamento')->references('id')->on('departamentos');
            $table->integer('ordem')->nullable();
            $table->boolean('atual')->nullable();
            $table->boolean('ativo')->default(AuxiliarDocumentoDepartamento::ATIVO);
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
        Schema::dropIfExists('auxiliar_documento_departamentos');
    }
}
