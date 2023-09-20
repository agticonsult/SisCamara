<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoHorarioVotacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_horario_votacaos')->insert([
            ['descricao' => 'Início da Votação', 'ativo' => 1],
            ['descricao' => 'Fim da Votação', 'ativo' => 1]
        ]);
    }
}
