<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_atos')->insert([
            ['descricao'=>'Ordinário', 'ativo'=>1],
            ['descricao'=>'Extraordinário', 'ativo'=>1]
        ]);
    }
}
