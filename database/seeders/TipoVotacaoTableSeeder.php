<?php

namespace Database\Seeders;

use App\Models\TipoVotacao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoVotacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_votacaos')->insert([
            ['descricao'=>'Aberta', 'ativo'=> TipoVotacao::ATIVO],
            ['descricao'=>'Fechada', 'ativo'=> TipoVotacao::ATIVO],
        ]);
    }
}
