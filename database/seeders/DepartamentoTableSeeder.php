<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\DepartamentoUsuario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::orderBy('cpf')->get();
        DB::table('departamentos')->insert([
            ['descricao' => 'Recursos Humanos', 'id_coordenador' => $users[0]->id, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => Departamento::ATIVO],
            ['descricao' => 'Financeiro', 'id_coordenador' => $users[1]->id, 'cadastradoPorUsuario' => $users[0]->id, 'ativo' => Departamento::ATIVO]
        ]);
        DB::table('departamento_usuarios')->insert([
            ['id_user' => $users[0]->id, 'id_departamento' => 1, 'ativo' => DepartamentoUsuario::ATIVO],
            ['id_user' => $users[1]->id, 'id_departamento' => 2, 'ativo' => DepartamentoUsuario::ATIVO]
        ]);
    }
}
