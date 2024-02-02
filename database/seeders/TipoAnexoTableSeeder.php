<?php

namespace Database\Seeders;

use App\Models\TipoAnexo;
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
            ['descricao'=>'Documento', 'ativo' => TipoAnexo::ATIVO],
            ['descricao'=>'Imagem', 'ativo' => TipoAnexo::ATIVO],
            ['descricao'=>'Áudio', 'ativo' => TipoAnexo::ATIVO],
            ['descricao'=>'Vídeo', 'ativo' => TipoAnexo::ATIVO]
        ]);
    }
}
