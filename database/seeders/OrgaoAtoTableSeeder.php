<?php

namespace Database\Seeders;

use App\Models\OrgaoAto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrgaoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orgao_atos')->insert([
            ['descricao' => 'Procuradoria Municipal', 'ativo' => OrgaoAto::ATIVO],
            ['descricao' => 'Polícia', 'ativo' => OrgaoAto::ATIVO],
            ['descricao' => 'Controladoria-Geral da União', 'ativo' => OrgaoAto::ATIVO],
            ['descricao' => 'Tribunais de Contas', 'ativo' => OrgaoAto::ATIVO]
        ]);
    }
}
