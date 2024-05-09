<?php

namespace Database\Seeders;

use App\Models\Entidade;
use App\Models\Estado;
use App\Models\Funcionalidade;
use App\Models\TipoFuncionalidade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuncionalidadeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entidades')->insert([
            ['descricao'=>'Ato', 'nomeEntidade'=>'Ato', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'AssuntoAto', 'nomeEntidade'=>'AssuntoAto', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Departamento', 'nomeEntidade'=>'Departamento', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'TipoAto', 'nomeEntidade'=>'TipoAto', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Autoridade', 'nomeEntidade'=>'Autoridade', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Publicação do Ato', 'nomeEntidade'=>'PublicacaoAto', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Grupo de Usuário', 'nomeEntidade'=>'Grupo', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Usuário', 'nomeEntidade'=>'User', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Perfil', 'nomeEntidade'=>'Perfil', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Auditoria', 'nomeEntidade'=>'Auditoria', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Finalidade Grupo de Usuário', 'nomeEntidade'=>'FinalidadeGrupo', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Filesize', 'nomeEntidade'=>'Filesize', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Anexo do Ato', 'nomeEntidade'=>'AnexoAto', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Repartição', 'nomeEntidade'=>'Reparticao', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Proposição', 'nomeEntidade'=>'Proposicao', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Modelo de Proposição', 'nomeEntidade'=>'ModeloProposicao', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Agente Político', 'nomeEntidade'=>'AgentePolitico', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Legislatura', 'nomeEntidade'=>'Legislatura', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Pleito Eleitoral', 'nomeEntidade'=>'PleitoEleitoral', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Votação Eletrônica', 'nomeEntidade'=>'VotacaoEletronica', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Vereador Votação', 'nomeEntidade'=>'VereadorVotacao', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Tipo Documento', 'nomeEntidade'=>'TipoDocumento', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Documento', 'nomeEntidade'=>'Documento', 'ativo'=> Entidade::ATIVO],
            ['descricao'=>'Gestão Administrativa', 'nomeEntidade'=>'GestaoAdministrativa', 'ativo'=> Entidade::ATIVO],
        ]);

        DB::table('tipo_funcionalidades')->insert([
            ['descricao'=>'Listagem', 'ativo'=> TipoFuncionalidade::ATIVO],
            ['descricao'=>'Cadastro', 'ativo'=> TipoFuncionalidade::ATIVO],
            ['descricao'=>'Alteração', 'ativo'=> TipoFuncionalidade::ATIVO],
            ['descricao'=>'Exclusão', 'ativo'=> TipoFuncionalidade::ATIVO],
            ['descricao'=>'Relatório', 'ativo'=> TipoFuncionalidade::ATIVO],
            ['descricao'=>'Validação', 'ativo'=> TipoFuncionalidade::ATIVO],
            ['descricao'=>'Finalização', 'ativo'=> TipoFuncionalidade::ATIVO],
            ['descricao'=>'Homologação', 'ativo'=> TipoFuncionalidade::ATIVO],
        ]);

        $entidades = Entidade::all();
        $tipo_funcionalidades = TipoFuncionalidade::all();

        foreach ($entidades as $e) {
            foreach ($tipo_funcionalidades as $tp) {
                Funcionalidade::create([
                    'id_entidade' => $e->id,
                    'id_tipo_funcionalidade' => $tp->id
                ]);
            }
        }
    }
}
