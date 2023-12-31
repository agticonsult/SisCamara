<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupos')->insert([
            ['nome'=>'Administrador', 'ativo'=>1],
            ['nome'=>'Usuário Externo', 'ativo'=>1],
            ['nome'=>'Funcionário', 'ativo'=>1],
        ]);
    }
}
