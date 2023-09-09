<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusProposicaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_proposicaos')->insert([
            ['descricao'=>'Pendente', 'ativo'=>1],
            ['descricao'=>'Em votação', 'ativo'=>1],
            ['descricao'=>'Negado', 'ativo'=>1],
            ['descricao'=>'Aprovado', 'ativo'=>1],
        ]);
    }
}
