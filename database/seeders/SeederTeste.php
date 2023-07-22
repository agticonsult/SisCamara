<?php

namespace Database\Seeders;

use App\Models\Permissao;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederTeste extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agile = Permissao::where('id_perfil', '=', 1)->first();
        $funcionario = Permissao::where('id_perfil', '=', 2)->first();
        $cliente = Permissao::where('id_perfil', '=', 3)->first();

        DB::table('grupos')->insert([
            ['nome'=>'Grupo 1', 'id_finalidade' => 1, 'ativo'=>1],
            ['nome'=>'Grupo 2', 'id_finalidade' => 2, 'ativo'=>1],
            ['nome'=>'Grupo 3', 'id_finalidade' => 3, 'ativo'=>1]
        ]);

        DB::table('membro_grupos')->insert([

            // Grupo 1
            ['id_grupo' => 1, 'id_user' => $funcionario->id_user, 'adm' => 0, 'ativo' => 1], //Funcionario
            ['id_grupo' => 1, 'id_user' => $agile->id_user, 'adm' => 0, 'ativo' => 1], //Agile

            // Grupo 2
            ['id_grupo' => 2, 'id_user' => $agile->id_user, 'adm' => 0, 'ativo' => 1], //Agile
            ['id_grupo' => 2, 'id_user' => $cliente->id_user, 'adm' => 0, 'ativo' => 1], //Cliente

            // Grupo 3
            ['id_grupo' => 3, 'id_user' => $funcionario->id_user, 'adm' => 0, 'ativo' => 1], //Funcionario
            ['id_grupo' => 3, 'id_user' => $cliente->id_user, 'adm' => 0, 'ativo' => 1] //Cliente
        ]);
    }
}
