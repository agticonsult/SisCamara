<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\User;
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
        $uuid1 = "26ce0b8c-c37c-4f79-926c-aa03af692886";
        $uuid2 = "20e40049-0bb4-44d2-a5dd-7f5edc8ba7da";
        $uuid3 = "b585df6f-45c9-43df-9a2d-1cc4a840ff46";
        $uuid4 = "e6ba7783-c32b-4ea8-bc0d-8149fdfdebce";

        DB::table('pessoas')->insert([
            ['pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA, 'nome' => 'Agile', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-01-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => Pessoa::ATIVO],
            ['pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA, 'nome' => 'Fulano', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-02-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => Pessoa::ATIVO],
            ['pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA, 'nome' => 'Ciclano', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => '2001-03-01', 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => Pessoa::ATIVO],
            ['pessoaJuridica' => Pessoa::PESSOA_JURIDICA, 'nome' => 'Empresa X', 'apelidoFantasia' => NULL, 'dt_nascimento_fundacao' => NULL,  'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now(), 'ativo' => Pessoa::ATIVO]
        ]);

        DB::table('users')->insert([
            [
                'id' => $uuid1, 'id_pessoa' => 1, 'id_grupo' => Grupo::ADMINISTRADOR, 'cpf' => '00000000000', 'cnpj' => NULL, 'email' => 'suporte@agile.inf.br', 'password' => Hash::make('sup2011@'),
                'tentativa_senha' => User::NAO_BLOQUEADO_TENTATIVA, 'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA, 'ativo' => User::ATIVO,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => User::EMAIL_CONFIRMADO
            ],
            [
                'id' => $uuid2, 'id_pessoa' => 2, 'id_grupo' => Grupo::POLITICO, 'cpf' => '11111111111', 'cnpj' => NULL, 'email' => 'funcionario1@funcionario.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => User::NAO_BLOQUEADO_TENTATIVA, 'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA, 'ativo' => User::ATIVO,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => User::EMAIL_CONFIRMADO
            ],
            [
                'id' => $uuid3, 'id_pessoa' => 3, 'id_grupo' => Grupo::EXTERNO, 'cpf' => '22222222222', 'cnpj' => NULL, 'email' => 'cliente1@cliente.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => User::NAO_BLOQUEADO_TENTATIVA, 'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA, 'ativo' => User::ATIVO,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => User::EMAIL_CONFIRMADO
            ],
            [
                'id' => $uuid4, 'id_pessoa' => 4, 'id_grupo' => Grupo::INTERNO, 'cpf' => NULL, 'cnpj' => '33333333333333', 'email' => 'suporte@empresa.inf.br', 'password' => Hash::make('123456'),
                'tentativa_senha' => User::NAO_BLOQUEADO_TENTATIVA, 'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA, 'ativo' => User::ATIVO,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), 'confirmacao_email' => User::EMAIL_CONFIRMADO
            ]
        ]);

        DB::table('perfil_users')->insert([
            ['id_user' => $uuid1, 'id_tipo_perfil' => Perfil::USUARIO_ADM, 'ativo' => PerfilUser::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid2, 'id_tipo_perfil' => Perfil::USUARIO_POLITICO, 'ativo' => PerfilUser::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid3, 'id_tipo_perfil' => Perfil::USUARIO_EXTERNO, 'ativo' => PerfilUser::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid4, 'id_tipo_perfil' => Perfil::USUARIO_INTERNO, 'ativo' => PerfilUser::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid1, 'id_tipo_perfil' => Perfil::USUARIO_INTERNO, 'ativo' => PerfilUser::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid2, 'id_tipo_perfil' => Perfil::USUARIO_INTERNO, 'ativo' => PerfilUser::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()]
        ]);

        DB::table('permissaos')->insert([
            ['id_user' => $uuid1, 'id_perfil' => Perfil::USUARIO_ADM, 'ativo' => Permissao::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid2, 'id_perfil' => Perfil::USUARIO_POLITICO, 'ativo' => Permissao::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid3, 'id_perfil' => Perfil::USUARIO_EXTERNO, 'ativo' => Permissao::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid4, 'id_perfil' => Perfil::USUARIO_INTERNO, 'ativo' => Permissao::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid1, 'id_perfil' => Perfil::USUARIO_INTERNO, 'ativo' => Permissao::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()],
            ['id_user' => $uuid2, 'id_perfil' => Perfil::USUARIO_INTERNO, 'ativo' => Permissao::ATIVO, 'cadastradoPorUsuario' => $uuid1, 'created_at' => Carbon::now()]
        ]);
    }
}
