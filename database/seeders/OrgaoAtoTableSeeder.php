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
            ['descricao' => 'Órgão 1', 'ativo' => OrgaoAto::ATIVO],
            ['descricao' => 'Órgão 2', 'ativo' => OrgaoAto::ATIVO],
            ['descricao' => 'Órgão 3', 'ativo' => OrgaoAto::ATIVO],
            ['descricao' => 'Órgão 4', 'ativo' => OrgaoAto::ATIVO]
        ]);
    }
}
