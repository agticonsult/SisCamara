<?php

namespace Database\Seeders;

use App\Models\Funcionalidade;
use App\Models\Perfil;
use App\Models\PerfilFuncionalidade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class PerfilFuncionalidadeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --------------------------Funcionalidades do ADM--------------------------
        $funcionalidadesADM = Funcionalidade::select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesADM as $f1) {
            PerfilFuncionalidade::create([
                'id_perfil' => Perfil::USUARIO_ADM,
                'id_funcionalidade' => $f1->id
            ]);
        }

        //--------------------------Funcionalidades do Político--------------------------
        $funcionalidadesPolitico = Funcionalidade::funcionalidadesPolitico();
        foreach ($funcionalidadesPolitico as $f2) {
            PerfilFuncionalidade::create([
                'id_perfil' => Perfil::USUARIO_POLITICO,
                'id_funcionalidade' => $f2->id
            ]);
        }

        //--------------------------Funcionalidades do usuário interno--------------------------
        $funcionalidadesUsuarioInterno = Funcionalidade::funcionalidadeUsuarioInterno();
        foreach ($funcionalidadesUsuarioInterno as $f3) {
            PerfilFuncionalidade::create([
                'id_perfil' => Perfil::USUARIO_INTERNO,
                'id_funcionalidade' => $f3->id
            ]);
        }

        //--------------------------Funcionalidades do usuário externo--------------------------
        $funcionalidadesUsuarioExterno = Funcionalidade::funcionalidadeUsuarioExterno();
        foreach ($funcionalidadesUsuarioExterno as $f4) {
            PerfilFuncionalidade::create([
                'id_perfil' => Perfil::USUARIO_EXTERNO,
                'id_funcionalidade' => $f4->id
            ]);
        }
    }
}

