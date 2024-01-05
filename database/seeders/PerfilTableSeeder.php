<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('perfils')->insert([
            ['descricao'=>'Administrador', 'ativo'=>1],
            ['descricao'=>'Vereador', 'ativo'=>1],
            ['descricao'=>'Usuário Externo', 'ativo'=>1],
            ['descricao'=>'Usuário Interno', 'ativo'=>1],
        ]);
    }
}
