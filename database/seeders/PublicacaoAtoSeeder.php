<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicacaoAtoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('publicacao_atos')->insert([
            ['descricao'=>'Publicação 1', 'ativo'=>1],
            ['descricao'=>'Publicação 2', 'ativo'=>1],
            ['descricao'=>'Publicação 3', 'ativo'=>1],
            ['descricao'=>'Publicação 4', 'ativo'=>1]
        ]);
    }
}
