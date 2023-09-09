<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalizacaoProposicaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('localizacao_proposicaos')->insert([
            ['descricao'=>'Protocolo', 'ativo'=>1],
            ['descricao'=>'Secretaria', 'ativo'=>1],
            ['descricao'=>'Relator', 'ativo'=>1],
            ['descricao'=>'Comissão', 'ativo'=>1],
            ['descricao'=>'Plenário', 'ativo'=>1]
        ]);
    }
}





