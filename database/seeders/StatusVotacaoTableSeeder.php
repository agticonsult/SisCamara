<?php

namespace Database\Seeders;

use App\Models\StatusVotacao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusVotacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_votacaos')->insert([
            ['descricao' => 'Pendente', 'ativo' => StatusVotacao::ATIVO],
            ['descricao' => 'Em votação', 'ativo' => StatusVotacao::ATIVO],
            ['descricao' => 'Votado', 'ativo' => StatusVotacao::ATIVO],
            ['descricao' => 'Encerrado', 'ativo' => StatusVotacao::ATIVO],
            ['descricao' => 'Pausado', 'ativo' => StatusVotacao::ATIVO]
        ]);
    }
}
