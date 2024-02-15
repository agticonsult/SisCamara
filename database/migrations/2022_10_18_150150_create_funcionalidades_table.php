<?php

use App\Models\Funcionalidade;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuncionalidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funcionalidades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_entidade')->unsigned()->nullable();
            $table->foreign('id_entidade')->references('id')->on('entidades');
            $table->integer('id_tipo_funcionalidade')->unsigned()->nullable();
            $table->foreign('id_tipo_funcionalidade')->references('id')->on('tipo_funcionalidades');
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(Funcionalidade::ATIVO);
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
        Schema::dropIfExists('funcionalidades');
    }
}
