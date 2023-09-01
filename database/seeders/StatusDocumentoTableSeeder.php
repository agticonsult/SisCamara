<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusDocumentoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_documentos')->insert([
            ['descricao'=>'Em votação', 'ativo'=>1],
            ['descricao'=>'Negado', 'ativo'=>1],
            ['descricao'=>'Aprovado', 'ativo'=>1],
        ]);
    }
}
