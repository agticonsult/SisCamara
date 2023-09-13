<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusVotacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_votacaos')->insert([
            ['descricao'=>'Pendente', 'ativo'=>1],
            ['descricao'=>'Em votação', 'ativo'=>1],
            ['descricao'=>'Votado', 'ativo'=>1],
        ]);
    }
}
