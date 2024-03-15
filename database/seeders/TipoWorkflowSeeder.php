<?php

namespace Database\Seeders;

use App\Models\TipoWorkflow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_workflows')->insert([
            ['descricao'=>'Tramitação automática', 'ativo'=> TipoWorkflow::ATIVO],
            ['descricao'=>'Tramitação manual', 'ativo'=> TipoWorkflow::ATIVO]
        ]);
    }
}
