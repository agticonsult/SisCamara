<?php

use App\Models\PleitoEleitoral;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePleitoEleitoralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pleito_eleitorals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ano_pleito')->nullable();
            $table->boolean('pleitoEspecial')->nullable()->default(0);
            $table->date('dataPrimeiroTurno')->nullable();
            $table->date('dataSegundoTurno')->nullable();
            $table->integer('id_legislatura')->unsigned()->nullable();
            $table->foreign('id_legislatura')->references('id')->on('legislaturas');
            $table->uuid('cadastradoPorUsuario')->nullable();
            $table->foreign('cadastradoPorUsuario')->references('id')->on('users');
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->foreign('inativadoPorUsuario')->references('id')->on('users');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(PleitoEleitoral::ATIVO);
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
        Schema::dropIfExists('pleito_eleitorals');
    }
}
