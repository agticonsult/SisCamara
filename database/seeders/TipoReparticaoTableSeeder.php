<?php

namespace Database\Seeders;

use App\Models\TipoReparticao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoReparticaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_reparticaos')->insert([
            ['descricao' => 'Tipo Reparticao 1', 'ativo' => TipoReparticao::ATIVO],
            ['descricao' => 'Tipo Reparticao 2', 'ativo' => TipoReparticao::ATIVO],
            ['descricao' => 'Tipo Reparticao 3', 'ativo' => TipoReparticao::ATIVO]
        ]);
    }
}
