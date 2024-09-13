<?php

use App\Models\Certificado;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('nome_original');
            $table->text('nome_hash');
            $table->text('diretorio'); // diretório localizado em storage/app/
            $table->string('password'); // senha do arquivo em hash
            $table->date('data_validade'); // data limite em que o certificado deve ficar no sistema, após a data o arquivo será excluído
            $table->string('tipo'); // tipo do certificado, PJ ou PF, A1 ou A3
            $table->string('nome_cert'); // tipo do certificado, PJ ou PF, A1 ou A3
            $table->uuid('id_user');
            $table->foreign('id_user')->references('id')->on('users');
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
        Schema::dropIfExists('certificados');
    }
}
