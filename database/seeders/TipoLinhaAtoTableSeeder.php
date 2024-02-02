<?php

namespace Database\Seeders;

use App\Models\TipoLinhaAto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoLinhaAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_linha_atos')->insert([
            ['descricao' => 'Texto Original', 'ativo' => TipoLinhaAto::ATIVO],
            ['descricao' => 'Texto Adicionado', 'ativo' => TipoLinhaAto::ATIVO]
        ]);
    }
}
