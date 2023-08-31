<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoReparticaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_reparticaos')->insert([
            ['descricao'=>'Tipo Reparticao 1', 'ativo'=>1],
            ['descricao'=>'Tipo Reparticao 2', 'ativo'=>1],
            ['descricao'=>'Tipo Reparticao 3', 'ativo'=>1]
        ]);
    }
}
