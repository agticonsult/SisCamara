<?php

namespace Database\Seeders;

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
            ['descricao'=>'Autoridade 1', 'ativo'=>1],
            ['descricao'=>'Autoridade 2', 'ativo'=>1],
            ['descricao'=>'Autoridade 3', 'ativo'=>1],
            ['descricao'=>'Autoridade 4', 'ativo'=>1]
        ]);
    }
}
