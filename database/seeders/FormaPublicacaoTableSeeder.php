<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormaPublicacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('forma_publicacao_atos')->insert([
            ['descricao'=>'Forma 1', 'ativo'=>1],
            ['descricao'=>'Forma 2', 'ativo'=>1],
            ['descricao'=>'Forma 3', 'ativo'=>1],
            ['descricao'=>'Forma 4', 'ativo'=>1]
        ]);
    }
}
