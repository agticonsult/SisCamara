<?php

namespace Database\Seeders;

use App\Models\Autoridade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AutoridadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('autoridades')->insert([
            ['descricao' => 'Autoridade 1', 'ativo' => Autoridade::ATIVO],
            ['descricao' => 'Autoridade 2', 'ativo' => Autoridade::ATIVO],
            ['descricao' => 'Autoridade 3', 'ativo' => Autoridade::ATIVO],
            ['descricao' => 'Autoridade 4', 'ativo' => Autoridade::ATIVO]
        ]);
    }
}
