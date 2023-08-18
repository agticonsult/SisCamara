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
            $table->id();
            $table->integer('tipo_relacao');
            $table->bigInteger('ato_principal');
            $table->bigInteger('ato_relacionado');
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
