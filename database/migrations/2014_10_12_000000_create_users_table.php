<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            // uuid pra ter mais seguranca
            $table->uuid('id')->primary();

            // dados pessoais
            $table->string('cpf')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('password');
            $table->string('email')->nullable();
            $table->string('telefone_celular')->nullable();
            $table->string('telefone_celular2')->nullable();

            // pessoa
            $table->bigInteger('id_pessoa')->unsigned()->nullable();
            $table->foreign('id_pessoa')->references('id')->on('pessoas');

            // grupo
            $table->integer('id_grupo')->unsigned()->nullable();
            $table->foreign('id_grupo')->references('id')->on('grupos');

            // perfil
            // $table->integer('id_perfil')->unsigned();
            // $table->foreign('id_perfil')->references('id')->on('perfils');

            // importacao
            $table->boolean('importado')->nullable();
            $table->bigInteger('id_importacao')->nullable();

            // tentativa de login
            $table->integer('tentativa_senha')->nullable()->default(0); // realiza a contagem de tentativa de senha
            $table->boolean('bloqueadoPorTentativa')->nullable(); //caso passe de 3 tentativas invalidas
            $table->timestamp('dataBloqueadoPorTentativa')->nullable();

            // recuperacao de senha
            $table->integer('envio_email_recuperacao')->nullable()->default(0); // contagem de emails enviados

            // confirmacao email
            $table->integer('envio_email_confirmacaoApi')->nullable()->default(0); //realiza a contagem de envio de emails para confirmacao de cadastro via API
            $table->integer('envio_email_confirmacao')->nullable()->default(0); // contagem de emails de confirmacao enviados
            $table->boolean('confirmacao_email')->nullable(); // cadastro confirmado ou nao
            $table->timestamp('dataHoraConfirmacaoEmail')->nullable(); // datahora de confirmacao

            // validacao
            $table->boolean('validado')->nullable();
            $table->uuid('validadoPorUsuario')->nullable();
            $table->timestamp('validadoEm')->nullable();

            // incluso por outro usuario
            $table->boolean('incluso')->nullable();
            $table->uuid('incluidoPorUsuario')->nullable();
            $table->timestamp('incluidoEm')->nullable();

            // ativo
            $table->uuid('inativadoPorUsuario')->nullable();
            $table->timestamp('dataInativado')->nullable();
            $table->text('motivoInativado')->nullable();
            $table->boolean('ativo')->default(User::ATIVO);
            // $table->boolean('ativo')->nullable();

            $table->timestamp('email_verified_at')->nullable(); // verificacao email
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}



