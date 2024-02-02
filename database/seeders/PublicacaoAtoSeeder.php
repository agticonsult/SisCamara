<?php

namespace Database\Seeders;

use App\Models\PublicacaoAto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicacaoAtoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('publicacao_atos')->insert([
            ['descricao' => 'Publicação 1', 'ativo' => PublicacaoAto::ATIVO],
            ['descricao' => 'Publicação 2', 'ativo' => PublicacaoAto::ATIVO],
            ['descricao' => 'Publicação 3', 'ativo' => PublicacaoAto::ATIVO],
            ['descricao' => 'Publicação 4', 'ativo' => PublicacaoAto::ATIVO]
        ]);
    }
}
