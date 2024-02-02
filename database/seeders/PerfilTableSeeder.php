<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('perfils')->insert([
            ['descricao' => 'Administrador', 'ativo'=> Perfil::ATIVO],
            ['descricao' => 'Político', 'ativo'=> Perfil::ATIVO],
            ['descricao' => 'Usuário Externo', 'ativo'=> Perfil::ATIVO],
            ['descricao' => 'Usuário Interno', 'ativo'=> Perfil::ATIVO],
        ]);
    }
}
