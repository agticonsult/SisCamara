<?php

namespace Database\Seeders;

use App\Models\Pessoa;
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
        $uuid5 = "e74ddef8-9ecc-48b5-9639-8863b76de156";
        $uuid6 = "21e1e559-4ddb-479f-acf0-6eb4e994a0ef";
        $uuid7 = "145407c5-a406-47b2-a7ea-fb290d89c99d";
        $uuid8 = "00c7a3fe-19a0-4c0a-b270-ff3a750e4e33";
        $uuid9 = "4b6f2076-49e1-4eac-a1e3-3df00dafa5a0";
        $uuid10 = "4d8c5e29-073a-4e3d-a83c-db5237220b91";
        $uuid11 = "93b235dd-5d60-4038-b66c-04230c7930d5";

        DB::table('pessoas')->insert([
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Agile', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Func 1 - Gerente Estadual', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Agricultor 1', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Colaborador 1', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Func 2 - Gerente Regional', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Func 3 - Extensionista', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Func 4 - Extensionista', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Agricultor 2', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Agricultor 3', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Agricultor 4', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1],
            ['pessoaJuridica' => 0, 'nomeCompleto' => 'Agricultor 5', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01',  'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00', 'ativo' => 1]
        ]);

        DB::table('users')->insert([
            [
                'id' => $uuid1, 'id_pessoa' => 1, 'cpf' => '00000000000', 'email' => 'suporte@agile.inf.br', 'password' => Hash::make('sup2011@'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 1, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid2, 'id_pessoa' => 2, 'cpf' => '11111111111', 'email' => 'funcionario1@funcionario.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 2, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid3, 'id_pessoa' => 3, 'cpf' => '22222222222', 'email' => 'cliente1@cliente.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 3, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid4, 'id_pessoa' => 4, 'cpf' => '33333333333', 'email' => 'colaborador1@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 2, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid5, 'id_pessoa' => 5, 'cpf' => '44444444444', 'email' => 'colaborador2@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 2, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid6, 'id_pessoa' => 6, 'cpf' => '55555555555', 'email' => 'colaborador3@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 2, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid7, 'id_pessoa' => 7, 'cpf' => '66666666666', 'email' => 'colaborador4@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 2, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid8, 'id_pessoa' => 8, 'cpf' => '77777777777', 'email' => 'colaborador5@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 3, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid9, 'id_pessoa' => 9, 'cpf' => '88888888888', 'email' => 'colaborador6@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 3, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid10, 'id_pessoa' => 10, 'cpf' => '99999999999', 'email' => 'colaborador7@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 3, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ],
            [
                'id' => $uuid11, 'id_pessoa' => 11, 'cpf' => '99999999998', 'email' => 'colaborador8@colaborador.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => 0, 'bloqueadoPorTentativa' => 0, 'id_tipo_perfil' => 3, 'ativo' => 1,
                'created_at' => '2021-09-24 23:47:10', 'updated_at' => '2021-09-24 23:47:10', 'confirmacao_email' => 1
            ]
        ]);

        DB::table('perfil_users')->insert([
            ['id_user' => $uuid1, 'id_tipo_perfil' => 1, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid2, 'id_tipo_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid3, 'id_tipo_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid4, 'id_tipo_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid5, 'id_tipo_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid6, 'id_tipo_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid7, 'id_tipo_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid8, 'id_tipo_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid9, 'id_tipo_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid10, 'id_tipo_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid11, 'id_tipo_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00']
        ]);

        DB::table('permissaos')->insert([
            ['id_user' => $uuid1, 'id_perfil' => 1, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid2, 'id_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid3, 'id_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid4, 'id_perfil' => 4, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid5, 'id_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid6, 'id_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid7, 'id_perfil' => 2, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid8, 'id_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid9, 'id_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid10, 'id_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
            ['id_user' => $uuid11, 'id_perfil' => 3, 'ativo' => 1, 'cadastradoPorUsuario' => $uuid1, 'created_at' => '2022-09-22 16:56:00'],
        ]);
    }
}
