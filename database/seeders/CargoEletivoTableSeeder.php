<?php

namespace Database\Seeders;

use App\Models\CargoEletivo;
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
            ['id'=>1, 'descricao'=>'Vereador', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>2, 'descricao'=>'Prefeito Municipal', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>3, 'descricao'=>'Vice-Prefeito', 'ativo'=> CargoEletivo::ATIVO],

            //Estadual
            ['id'=>4, 'descricao'=>'Deputado Estadual', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>5, 'descricao'=>'Deputado Distrital', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>6, 'descricao'=>'Governador Estadual', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>7, 'descricao'=>'Governador Distrital', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>8, 'descricao'=>'Vice-Governador', 'ativo'=> CargoEletivo::ATIVO],

            //Nacional
            ['id'=>9, 'descricao'=>'Deputado Federal', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>10, 'descricao'=>'Senador', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>11, 'descricao'=>'Presidente da República', 'ativo'=> CargoEletivo::ATIVO],
            ['id'=>12, 'descricao'=>'Vice-Presidente', 'ativo'=> CargoEletivo::ATIVO]
        ]);
    }
}
