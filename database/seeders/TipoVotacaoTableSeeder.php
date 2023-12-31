<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoVotacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_votacaos')->insert([
            ['descricao'=>'Aberta', 'ativo'=>1],
            ['descricao'=>'Fechada', 'ativo'=>1],
        ]);
    }
}
