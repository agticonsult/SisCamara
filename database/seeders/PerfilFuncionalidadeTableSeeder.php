<?php

namespace Database\Seeders;

use App\Models\Funcionalidade;
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
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 1;
            $perfilFuncionalidade->id_funcionalidade = $f1->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }

        //--------------------------Funcionalidades do Vereador--------------------------
        $funcionalidadesVereador = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where(function (Builder $query) {
                return
                    $query->where('entidades.nomeEntidade', '=', 'Documento');
                    })
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesVereador as $f2) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 2;
            $perfilFuncionalidade->id_funcionalidade = $f2->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }
    }
}

