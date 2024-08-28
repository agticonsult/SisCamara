<?php

namespace Database\Seeders;

use App\Models\TipoAto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_atos')->insert([
            ['descricao' => 'Ordinário', 'ativo'=> TipoAto::ATIVO],
            ['descricao' => 'Extraordinário', 'ativo'=> TipoAto::ATIVO],
            ['descricao' => 'Constitutivo', 'ativo'=> TipoAto::ATIVO],
            ['descricao' => 'Normativo', 'ativo'=> TipoAto::ATIVO]
        ]);
    }
}
