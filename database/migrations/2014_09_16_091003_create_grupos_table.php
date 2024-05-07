<?php

use App\Models\Grupo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('nome');
            // $table->integer('id_finalidade')->unsigned()->nullable();
            // $table->foreign('id_finalidade')->references('id')->on('finalidade_grupos');
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(Grupo::ATIVO);
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
        Schema::dropIfExists('grupos');
    }
}
