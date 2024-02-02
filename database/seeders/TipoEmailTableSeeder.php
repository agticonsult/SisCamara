<?php

namespace Database\Seeders;

use App\Models\TipoEmail;
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
            ['descricao' => 'Troca de Senha', 'ativo' => TipoEmail::ATIVO],
            ['descricao' => 'Confirmação de senha', 'ativo' => TipoEmail::ATIVO],
            ['descricao' => 'Confirmação de e-mail', 'ativo' => TipoEmail::ATIVO]
        ]);
    }
}
