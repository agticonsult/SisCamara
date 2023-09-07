<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoEletivoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_eletivos')->insert([

            //Municipal
            ['id'=>1, 'descricao'=>'Vereador', 'ativo'=>1],
            ['id'=>2, 'descricao'=>'Prefeito Municipal', 'ativo'=>1],
            ['id'=>3, 'descricao'=>'Vice-Prefeito', 'ativo'=>1],

            //Estadual
            ['id'=>4, 'descricao'=>'Deputado Estadual', 'ativo'=>1],
            ['id'=>5, 'descricao'=>'Deputado Distrital', 'ativo'=>1],
            ['id'=>6, 'descricao'=>'Governador Estadual', 'ativo'=>1],
            ['id'=>7, 'descricao'=>'Governador Distrital', 'ativo'=>1],
            ['id'=>8, 'descricao'=>'Vice-Governador', 'ativo'=>1],

            //Nacional
            ['id'=>9, 'descricao'=>'Deputado Federal', 'ativo'=>1],
            ['id'=>10, 'descricao'=>'Senador', 'ativo'=>1],
            ['id'=>11, 'descricao'=>'Presidente da República', 'ativo'=>1],
            ['id'=>12, 'descricao'=>'Vice-Presidente', 'ativo'=>1]
        ]);
    }
}
