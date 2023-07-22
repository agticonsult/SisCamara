<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAnexoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_anexos')->insert([
            ['descricao'=>'Documento', 'ativo'=>1],
            ['descricao'=>'Imagem', 'ativo'=>1],
            ['descricao'=>'Áudio', 'ativo'=>1],
            ['descricao'=>'Vídeo', 'ativo'=>1]
        ]);
    }
}
