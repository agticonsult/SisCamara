<?php

use App\Models\DepartamentoUsuario;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentoUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departamento_usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
            $table->integer('id_departamento')->unsigned()->nullable();
            $table->foreign('id_departamento')->references('id')->on('departamentos');
            // $table->uuid('cadastradoPorUsuario')->nullable();
            // $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->boolean('ativo')->default(DepartamentoUsuario::ATIVO);
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
        Schema::dropIfExists('departamento_usuarios');
    }
}
