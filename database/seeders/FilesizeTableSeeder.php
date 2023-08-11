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
            ['descricao'=>'Anexo do Ato', 'ativo'=>1],
        ]);

        DB::table('filesizes')->insert([
            ['mb' => 10, 'id_tipo_filesize' => 1, 'ativo' => 1],
        ]);
    }
}

