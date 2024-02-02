<?php

namespace Database\Seeders;

use App\Models\ClassificacaoAto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassificacaoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('classificacao_atos')->insert([
            ['descricao'=>'Lei Ordinária', 'ativo'=> ClassificacaoAto::ATIVO],
            ['descricao'=>'Lei Complementar', 'ativo'=> ClassificacaoAto::ATIVO],
            ['descricao'=>'Código', 'ativo'=> ClassificacaoAto::ATIVO]
        ]);
    }
}
