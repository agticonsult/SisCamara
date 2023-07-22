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
        $funcionalidadesADM = Funcionalidade::leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Exclusão')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Relatório')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Validação')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Finalização')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Homologação');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesADM as $f1) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 1;
            $perfilFuncionalidade->id_funcionalidade = $f1->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }

        //--------------------------Funcionalidades do Funcionário--------------------------
        $funcionalidadesFunc = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where(function (Builder $query) {
                return
                    $query->where('entidades.nomeEntidade', '=', 'Agricultor')
                        ->orWhere('entidades.nomeEntidade', '=', 'Organizacao')
                        ->orWhere('entidades.nomeEntidade', '=', 'Chat')
                        ->orWhere('entidades.nomeEntidade', '=', 'HorarioAtendimento')
                        ->orWhere('entidades.nomeEntidade', '=', 'DataAtendimento')
                        ->orWhere('entidades.nomeEntidade', '=', 'Agendamento')
                        ->orWhere('entidades.nomeEntidade', '=', 'Atendimento')
                        ->orWhere('entidades.nomeEntidade', '=', 'EncaminharAtendimento')
                        ->orWhere('entidades.nomeEntidade', '=', 'Evento')
                        ->orWhere('entidades.nomeEntidade', '=', 'Acervo')
                        ->orWhere('entidades.nomeEntidade', '=', 'Entrevista');
                    })
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Exclusão')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Relatório')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Validação')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Finalização')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Homologação');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesFunc as $f2) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 2;
            $perfilFuncionalidade->id_funcionalidade = $f2->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }

        $funcionalidadesFunc2 = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where('entidades.nomeEntidade', '=', 'Processo')
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem*')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração*')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Relatório*');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesFunc2 as $f22) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 2;
            $perfilFuncionalidade->id_funcionalidade = $f22->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }

        //--------------------------Funcionalidades do Cliente/Agricultor--------------------------
        $funcionalidadesAgri = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where(function (Builder $query) {
                return
                    $query->where('entidades.nomeEntidade', '=', 'Agricultor')
                        ->orWhere('entidades.nomeEntidade', '=', 'Chat')
                        ->orWhere('entidades.nomeEntidade', '=', 'Agendamento')
                        ->orWhere('entidades.nomeEntidade', '=', 'Evento')
                        ->orWhere('entidades.nomeEntidade', '=', 'Acervo')
                        ->orWhere('entidades.nomeEntidade', '=', 'VisitaAcervo')
                        ->orWhere('entidades.nomeEntidade', '=', 'Entrevista');
            })
            ->where(function (Builder $query) {
                return
                    $query->where('tipo_funcionalidades.descricao', '=', 'Listagem*')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Cadastro*')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Alteração*')
                        ->orWhere('tipo_funcionalidades.descricao', '=', 'Exclusão*');
            })
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesAgri as $f3) {
            $perfilFuncionalidade = new PerfilFuncionalidade();
            $perfilFuncionalidade->id_perfil = 3;
            $perfilFuncionalidade->id_funcionalidade = $f3->id;
            $perfilFuncionalidade->ativo = 1;
            $perfilFuncionalidade->save();
        }

        //--------------------------Funcionalidades Mensagem--------------------------
        $funcionalidadesMsg = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
            ->leftJoin('tipo_funcionalidades', 'tipo_funcionalidades.id', '=', 'funcionalidades.id_tipo_funcionalidade')
            ->where('entidades.nomeEntidade', '=', 'Mensagem')
            ->where('tipo_funcionalidades.descricao', '=', 'Cadastro')
            ->select('funcionalidades.id')
            ->get();

        foreach ($funcionalidadesMsg as $f4) {
            $perfilFuncionalidade2 = new PerfilFuncionalidade();
            $perfilFuncionalidade2->id_perfil = 2;
            $perfilFuncionalidade2->id_funcionalidade = $f4->id;
            $perfilFuncionalidade2->ativo = 1;
            $perfilFuncionalidade2->save();

            $perfilFuncionalidade3 = new PerfilFuncionalidade();
            $perfilFuncionalidade3->id_perfil = 3;
            $perfilFuncionalidade3->id_funcionalidade = $f4->id;
            $perfilFuncionalidade3->ativo = 1;
            $perfilFuncionalidade3->save();
        }
    }
}

