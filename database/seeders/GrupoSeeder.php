<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupos')->insert([
            ['nome' => 'Interno', 'ativo' => Grupo::ATIVO],
            ['nome' => 'Externo', 'ativo' => Grupo::ATIVO],
            ['nome' => 'Político', 'ativo' => Grupo::ATIVO],
        ]);
    }
}
