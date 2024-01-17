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

        //--------------------------Funcionalidades do Político--------------------------
        $funcionalidadesUsuarioInterno = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where(function (Builder $query) {
                return
                    $query->where('entidades.nomeEntidade', '=', 'Ato')
                        ->orWhere('entidades.nomeEntidade', '=', 'PublicacaoAto')
                        ->orWhere('entidades.nomeEntidade', '=', 'Perfil')
                        ->orWhere('entidades.nomeEntidade', '=', 'AnexoAto')
                        ->orWhere('entidades.nomeEntidade', '=', 'Reparticao')
                        ->orWhere('entidades.nomeEntidade', '=', 'Proposicao')
                        ->orWhere('entidades.nomeEntidade', '=', 'ModeloProposicao')
                        ->orWhere('entidades.nomeEntidade', '=', 'AgentePolitico')
                        ->orWhere('entidades.nomeEntidade', '=', 'Legislatura')
                        ->orWhere('entidades.nomeEntidade', '=', 'PleitoEleitoral')
                        ->orWhere('entidades.nomeEntidade', '=', 'VotacaoEletronica')
                        ->orWhere('entidades.nomeEntidade', '=', 'VereadorVotacao');
                })
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Exclusão');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesUsuarioInterno as $f2) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 2;
            $perfilFuncionalidade->id_funcionalidade = $f2->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }

        //--------------------------Funcionalidades do usuário interno--------------------------
        $funcionalidadesUsuarioInterno = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where(function (Builder $query) {
                return
                    $query->where('entidades.nomeEntidade', '=', 'Ato')
                        ->orWhere('entidades.nomeEntidade', '=', 'PublicacaoAto')
                        ->orWhere('entidades.nomeEntidade', '=', 'Perfil')
                        ->orWhere('entidades.nomeEntidade', '=', 'AnexoAto')
                        ->orWhere('entidades.nomeEntidade', '=', 'Reparticao')
                        ->orWhere('entidades.nomeEntidade', '=', 'Proposicao')
                        ->orWhere('entidades.nomeEntidade', '=', 'ModeloProposicao')
                        ->orWhere('entidades.nomeEntidade', '=', 'AgentePolitico')
                        ->orWhere('entidades.nomeEntidade', '=', 'Legislatura')
                        ->orWhere('entidades.nomeEntidade', '=', 'PleitoEleitoral');
                })
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Exclusão');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesUsuarioInterno as $f3) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 4;
            $perfilFuncionalidade->id_funcionalidade = $f3->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }

        //--------------------------Funcionalidades do usuário externo--------------------------
        $funcionalidadesUsuarioExterno = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where(function (Builder $query) {
                return
                    $query->where('entidades.nomeEntidade', '=', 'Ato')
                        ->orWhere('entidades.nomeEntidade', '=', 'Perfil');
                })
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Exclusão');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesUsuarioExterno as $f4) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 3;
            $perfilFuncionalidade->id_funcionalidade = $f4->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }
    }
}

