<?php

namespace Database\Seeders;

use App\Models\Permissao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PleitoEleitoralTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agile = Permissao::where('id_perfil', '=', 1)->first();

        DB::table('pleito_eleitorals')->insert([
            ['ano_pleito' => 2022, 'inicio_mandato' => 2023, 'fim_mandato' => 2026, 'pleitoEspecial' => false, 'dataPrimeiroTurno' => '2022-10-02',
                'dataSegundoTurno' => '2022-10-30', 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
        ]);

        DB::table('pleito_cargos')->insert([

            //Pleito de 2022
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 4, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 5, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 6, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 7, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 8, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 9, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 10, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 11, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
            ['id_pleito_eleitoral' => 1, 'id_cargo_eletivo' => 12, 'cadastradoPorUsuario' => $agile->id_user, 'ativo' => 1],
        ]);

    }
}



