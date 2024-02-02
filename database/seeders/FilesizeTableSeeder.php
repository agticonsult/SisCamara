<?php

namespace Database\Seeders;

use App\Models\Filesize;
use App\Models\TipoFilesize;
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
            ['descricao'=>'Anexo do Ato', 'ativo'=> TipoFilesize::ATIVO],
            ['descricao'=>'Foto de Perfil', 'ativo'=> TipoFilesize::ATIVO]
        ]);

        DB::table('filesizes')->insert([
            ['mb' => 10, 'id_tipo_filesize' => 1, 'ativo' => Filesize::ATIVO],
            ['mb' => 10, 'id_tipo_filesize' => 2, 'ativo' => Filesize::ATIVO]
        ]);
    }
}

