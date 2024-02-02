<?php

namespace Database\Seeders;

use App\Models\AssuntoAto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssuntoAtoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('assunto_atos')->insert([
            ['descricao' => 'Assunto 1', 'ativo' => AssuntoAto::ATIVO],
            ['descricao' => 'Assunto 2', 'ativo' => AssuntoAto::ATIVO],
            ['descricao' => 'Assunto 3', 'ativo' => AssuntoAto::ATIVO],
            ['descricao' => 'Assunto 4', 'ativo' => AssuntoAto::ATIVO]
        ]);
    }
}
