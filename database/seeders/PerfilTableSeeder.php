<?php

namespace Database\Seeders;

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
        DB::table('abrangencias')->insert([
            ['descricao'=>'Estadual', 'ativo'=>1],
            ['descricao'=>'Mesorregional', 'ativo'=>1],
            ['descricao'=>'Regional', 'ativo'=>1],
            ['descricao'=>'Municipal', 'ativo'=>1],
        ]);

        DB::table('tipo_perfils')->insert([
            ['descricao'=>'Administrador', 'ativo'=>1],
            ['descricao'=>'Funcionário', 'ativo'=>1],
            ['descricao'=>'Cliente', 'ativo'=>1],
            ['descricao'=>'Colaborador', 'ativo'=>1]
        ]);

        DB::table('perfils')->insert([
            ['descricao'=>'Administrador', 'id_abrangencia'=>1, 'id_tipo_perfil' => 1, 'ativo'=>1],
            ['descricao'=>'Funcionário', 'id_abrangencia'=>1, 'id_tipo_perfil' => 2, 'ativo'=>1],
            ['descricao'=>'Agricultor', 'id_abrangencia'=>4, 'id_tipo_perfil' => 3, 'ativo'=>1],
            ['descricao'=>'Gerente', 'id_abrangencia'=>1,'id_tipo_perfil' => 2,  'ativo'=>1]
        ]);
    }
}
