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
            ['descricao' => 'Nomeação servidores públicos', 'ativo' => AssuntoAto::ATIVO],
            ['descricao' => 'Discussão e votação de projetos de lei', 'ativo' => AssuntoAto::ATIVO],
            ['descricao' => 'Aprovação de orçamento', 'ativo' => AssuntoAto::ATIVO],
            ['descricao' => 'Autorização e homologação de processos licitatórios', 'ativo' => AssuntoAto::ATIVO]
        ]);
    }
}
