<?php

namespace Database\Seeders;

use App\Models\StatusDocumento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusDocTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_documentos')->insert([
            ['descricao'=> 'Aprovado', 'ativo' => StatusDocumento::ATIVO],
            ['descricao'=> 'Reprovado', 'ativo' => StatusDocumento::ATIVO],
            ['descricao'=> 'Criado', 'ativo' => StatusDocumento::ATIVO],
            ['descricao'=> 'Finalizado', 'ativo' => StatusDocumento::ATIVO],
            ['descricao'=> 'Atualizado', 'ativo' => StatusDocumento::ATIVO]
        ]);
    }
}
