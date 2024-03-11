<?php

namespace Database\Seeders;

use App\Models\StatusDepartamentoDocumento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusDepDocTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_departamento_documentos')->insert([
            ['descricao'=> 'Aprovado', 'ativo' => StatusDepartamentoDocumento::ATIVO],
            ['descricao'=> 'Reprovado', 'ativo' => StatusDepartamentoDocumento::ATIVO],
            ['descricao'=> 'Criação', 'ativo' => StatusDepartamentoDocumento::ATIVO],
            ['descricao'=> 'Finalizado', 'ativo' => StatusDepartamentoDocumento::ATIVO]
        ]);
    }
}
