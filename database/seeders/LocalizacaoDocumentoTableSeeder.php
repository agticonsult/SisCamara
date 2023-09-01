<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalizacaoDocumentoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('localizacao_documentos')->insert([
            ['descricao'=>'Localização 1', 'ativo'=>1],
            ['descricao'=>'Localização 2', 'ativo'=>1],
            ['descricao'=>'Localização 3', 'ativo'=>1],
            ['descricao'=>'Localização 4', 'ativo'=>1]
        ]);
    }
}
