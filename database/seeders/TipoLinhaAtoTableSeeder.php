<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoLinhaAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_linha_atos')->insert([
            ['descricao' => 'Texto Original', 'ativo' => 1],
            ['descricao' => 'Texto Adicionado', 'ativo' => 1]
        ]);
    }
}
