<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupos')->insert([
            ['descricao'=>'Administrador', 'ativo'=>1],
            ['descricao'=>'Usuário Externo', 'ativo'=>1],
            ['descricao'=>'Funcionário', 'ativo'=>1],
        ]);
    }
}
