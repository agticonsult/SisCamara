<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEmailTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_emails')->insert([
            ['descricao' => 'Troca de Senha', 'ativo' => 1],
            ['descricao' => 'Confirmação de senha', 'ativo' => 1],
            ['descricao' => 'Confirmação de e-mail', 'ativo' => 1]
        ]);
    }
}
