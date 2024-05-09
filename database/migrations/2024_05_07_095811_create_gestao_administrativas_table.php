<?php

use App\Models\GestaoAdministrativa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGestaoAdministrativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestao_administrativas', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('aprovacaoCadastro')->nullable();
            $table->boolean('recebimentoDocumento')->nullable();

            $table->integer('id_departamento')->unsigned()->nullable();
            $table->foreign('id_departamento')->references('id')->on('departamentos');

            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(GestaoAdministrativa::ATIVO);
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
        Schema::dropIfExists('gestao_administrativas');
    }
}
