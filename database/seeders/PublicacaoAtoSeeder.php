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
            ['descricao' => 'Diário Oficial do Município, Edição nº 456, de 15 de julho de 2024', 'ativo' => PublicacaoAto::ATIVO]
        ]);
    }
}
