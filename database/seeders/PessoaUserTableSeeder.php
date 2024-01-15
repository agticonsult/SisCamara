<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class PessoaUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $uuid1 = Uuid::uuid4();
        // $uuid2 = Uuid::uuid4();
        // $uuid3 = Uuid::uuid4();
        // $uuid4 = Uuid::uuid4();
        // $uuid5 = Uuid::uuid4();
        // $uuid6 = Uuid::uuid4();
        // $uuid7 = Uuid::uuid4();
        // $uuid8 = Uuid::uuid4();
        // $uuid9 = Uuid::uuid4();
        // $uuid10 = Uuid::uuid4();
        // $uuid11 = Uuid::uuid4();

        $uuid1 = "26ce0b8c-c37c-4f79-926c-aa03af692886";
        $uuid2 = "20e40049-0bb4-44d2-a5dd-7f5edc8ba7da";
        $uuid3 = "b585df6f-45c9-43df-9a2d-1cc4a840ff46";
        $uuid4 = "e6ba7783-c32b-4ea8-bc0d-8149fdfdebce";

        DB::table('pessoas')->insert([
            ['pessoaJuridica' => 0, 'nome' => 'Agile', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nome' => 'Fulano', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-02-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nome' => 'Ciclano', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-03-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nome' => 'Beltrano', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-04-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => 1],
        ]);

        DB::table('users')->insert([
            [
                'id' => $uuid1, 'id_pessoa' => 1, 'cpf' => '00000000000', 'email' => 'suporte@agile.inf.br', 'password' => Hash::make('sup2011@'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'ativo' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid2, 'id_pessoa' => 2, 'cpf' => '11111111111', 'email' => 'funcionario1@funcionario.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'ativo' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid3, 'id_pessoa' => 3, 'cpf' => '22222222222', 'email' => 'cliente1@cliente.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'ativo' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid4, 'id_pessoa' => 4, 'cpf' => '33333333333', 'email' => 'colaborador1@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'ativo' => 1,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => 1
            ]
        ]);

        DB::table('perfil_users')->insert([
            ['id_user' => $uuid1, 'id_tipo_perfil' => 1, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid2, 'id_tipo_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid3, 'id_tipo_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid4, 'id_tipo_perfil' => 4, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
        ]);

        DB::table('permissaos')->insert([
            ['id_user' => $uuid1, 'id_perfil' => 1, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid2, 'id_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid3, 'id_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid4, 'id_perfil' => 4, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()]
        ]);
    }
}
