<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_documentos')->insert([
            ['descricao' => 'Ofício', 'ativo' => TipoDocumento::ATIVO],
            ['descricao' => 'Protocolo', 'ativo' => TipoDocumento::ATIVO],
            ['descricao' => 'Requerimento', 'ativo' => TipoDocumento::ATIVO],
            ['descricao' => 'Solicitação', 'ativo' => TipoDocumento::ATIVO]
        ]);
    }
}
