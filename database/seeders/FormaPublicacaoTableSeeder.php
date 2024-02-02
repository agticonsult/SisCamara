<?php

namespace Database\Seeders;

use App\Models\FormaPublicacaoAto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormaPublicacaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('forma_publicacao_atos')->insert([
            ['descricao' => 'Forma 1', 'ativo' => FormaPublicacaoAto::ATIVO],
            ['descricao' => 'Forma 2', 'ativo' => FormaPublicacaoAto::ATIVO],
            ['descricao' => 'Forma 3', 'ativo' => FormaPublicacaoAto::ATIVO],
            ['descricao' => 'Forma 4', 'ativo' => FormaPublicacaoAto::ATIVO]
        ]);
    }
}
