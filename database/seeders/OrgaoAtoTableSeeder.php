<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrgaoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orgao_atos')->insert([
            ['descricao'=>'Órgão 1', 'ativo'=>1],
            ['descricao'=>'Órgão 2', 'ativo'=>1],
            ['descricao'=>'Órgão 3', 'ativo'=>1],
            ['descricao'=>'Órgão 4', 'ativo'=>1]
        ]);
    }
}
