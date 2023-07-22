<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilesizeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_filesizes')->insert([
            ['descricao'=>'Foto do Perfil', 'ativo'=>1],
            ['descricao'=>'Anexo do Processo', 'ativo'=>1],
            ['descricao'=>'Anexo do Chat', 'ativo'=>1],
            ['descricao'=>'Imagem do acervo', 'ativo'=>1],
            ['descricao'=>'Documento do acervo', 'ativo'=>1],
            ['descricao'=>'Imagem do evento', 'ativo'=>1],
        ]);

        DB::table('filesizes')->insert([
            ['mb' => 2, 'id_tipo_filesize' => 1, 'ativo' => 1],
            ['mb' => 2, 'id_tipo_filesize' => 2, 'ativo' => 1],
            ['mb' => 2, 'id_tipo_filesize' => 3, 'ativo' => 1],
            ['mb' => 5, 'id_tipo_filesize' => 4, 'ativo' => 1],
            ['mb' => 10, 'id_tipo_filesize' => 5, 'ativo' => 1],
            ['mb' => 5, 'id_tipo_filesize' => 6, 'ativo' => 1],
        ]);
    }
}

