<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssuntoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('assunto_atos')->insert([
            ['descricao'=>'Assunto 1', 'ativo'=>1],
            ['descricao'=>'Assunto 2', 'ativo'=>1],
            ['descricao'=>'Assunto 3', 'ativo'=>1],
            ['descricao'=>'Assunto 4', 'ativo'=>1]
        ]);
    }
}
