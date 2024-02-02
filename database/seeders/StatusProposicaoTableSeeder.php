<?php

namespace Database\Seeders;

use App\Models\StatusProposicao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusProposicaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_proposicaos')->insert([
            ['descricao' => 'Pendente', 'ativo'=> StatusProposicao::ATIVO],
            ['descricao' => 'Em votação', 'ativo'=> StatusProposicao::ATIVO],
            ['descricao' => 'Negado', 'ativo'=> StatusProposicao::ATIVO],
            ['descricao' => 'Aprovado', 'ativo'=> StatusProposicao::ATIVO],
        ]);
    }
}
