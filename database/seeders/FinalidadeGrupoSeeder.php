<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinalidadeGrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('finalidade_grupos')->insert([
            ['descricao' => 'Atendimento', 'ativo' => 1],
            ['descricao' => 'Programas', 'ativo' => 1],
            ['descricao' => 'Permissão', 'ativo' => 1],
            ['descricao' => 'Chat', 'ativo'=>1]
        ]);
    }
}
