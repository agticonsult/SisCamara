<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassificacaoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classificacao_atos')->insert([
            ['descricao'=>'Lei Ordinária', 'ativo'=>1],
            ['descricao'=>'Lei Complementar', 'ativo'=>1],
            ['descricao'=>'Código', 'ativo'=>1]
        ]);
    }
}
