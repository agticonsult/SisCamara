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
            // ['descricao'=>'Pessoa', 'nomeEntidade'=>'Pessoa', 'ativo'=>1],
            ['descricao'=>'Grupo de Usuário', 'nomeEntidade'=>'Grupo', 'ativo'=>1],
            ['descricao'=>'Programa', 'nomeEntidade'=>'Programa', 'ativo'=>1],
            ['descricao'=>'Importação', 'nomeEntidade'=>'Importacao', 'ativo'=>1],
            ['descricao'=>'Usuário', 'nomeEntidade'=>'User', 'ativo'=>1],
            ['descricao'=>'Perfil', 'nomeEntidade'=>'Perfil', 'ativo'=>1],
            ['descricao'=>'Auditoria', 'nomeEntidade'=>'Auditoria', 'ativo'=>1],
            ['descricao'=>'Finalidade Grupo de Usuário', 'nomeEntidade'=>'FinalidadeGrupo', 'ativo'=>1],
            ['descricao'=>'Agricultor', 'nomeEntidade'=>'Agricultor', 'ativo'=>1],
            ['descricao'=>'Organização', 'nomeEntidade'=>'Organizacao', 'ativo'=>1],
            ['descricao'=>'Processo', 'nomeEntidade'=>'Processo', 'ativo'=>1],
            ['descricao'=>'Chat', 'nomeEntidade'=>'Chat', 'ativo'=>1],
            ['descricao'=>'Mensagem', 'nomeEntidade'=>'Mensagem', 'ativo'=>1],
            ['descricao'=>'Horário de Atendimento', 'nomeEntidade'=>'HorarioAtendimento', 'ativo'=>1],
            ['descricao'=>'Agendamento', 'nomeEntidade'=>'Agendamento', 'ativo'=>1],
            ['descricao'=>'Atendimento', 'nomeEntidade'=>'Atendimento', 'ativo'=>1],
            ['descricao'=>'Encaminhar Atendimento', 'nomeEntidade'=>'EncaminharAtendimento', 'ativo'=>1],
            ['descricao'=>'Filesize', 'nomeEntidade'=>'Filesize', 'ativo'=>1],
            ['descricao'=>'Tipo de Evento', 'nomeEntidade'=>'TipoEvento', 'ativo'=>1],
            ['descricao'=>'Evento', 'nomeEntidade'=>'Evento', 'ativo'=>1],
            ['descricao'=>'Data de Atendimento', 'nomeEntidade'=>'DataAtendimento', 'ativo'=>1],
            ['descricao'=>'Acervo', 'nomeEntidade'=>'Acervo', 'ativo'=>1],
            ['descricao'=>'VisitaAcervo', 'nomeEntidade'=>'VisitaAcervo', 'ativo'=>1],
            ['descricao'=>'Entrevista', 'nomeEntidade'=>'Entrevista', 'ativo'=>1],
            ['descricao'=>'Composição Familiar', 'nomeEntidade'=>'ComposicaoFamiliar', 'ativo'=>1],
        ]);

        DB::table('tipo_funcionalidades')->insert([
            ['descricao'=>'Listagem', 'ativo'=>1],
            ['descricao'=>'Cadastro', 'ativo'=>1],
            ['descricao'=>'Alteração', 'ativo'=>1],
            ['descricao'=>'Exclusão', 'ativo'=>1],
            ['descricao'=>'Relatório', 'ativo'=>1],
            ['descricao'=>'Validação', 'ativo'=>1],
            ['descricao'=>'Finalização', 'ativo'=>1],
            ['descricao'=>'Homologação', 'ativo'=>1],
            ['descricao'=>'Listagem*', 'ativo'=>1],
            ['descricao'=>'Cadastro*', 'ativo'=>1],
            ['descricao'=>'Alteração*', 'ativo'=>1],
            ['descricao'=>'Exclusão*', 'ativo'=>1],
        ]);

        $entidades = Entidade::all();
        $tipo_funcionalidades = TipoFuncionalidade::all();

        foreach ($entidades as $e) {
            foreach ($tipo_funcionalidades as $tp) {
                $funcionalidade = new Funcionalidade();
                $funcionalidade->id_entidade = $e->id;
                $funcionalidade->id_tipo_funcionalidade = $tp->id;
                $funcionalidade->ativo = 1;
                $funcionalidade->save();
            }
        }
        // DB::table('funcionalidades')->insert([

        //     // Pessoa
        //     ['id_entidade'=>1, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>1, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>1, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>1, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>1, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Grupo de Usuário
        //     ['id_entidade'=>2, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>2, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>2, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>2, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>2, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Programa
        //     ['id_entidade'=>3, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>3, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>3, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>3, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>3, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Importação
        //     ['id_entidade'=>4, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>4, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>4, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>4, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>4, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Usuário
        //     ['id_entidade'=>5, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>5, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>5, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>5, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>5, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Perfil
        //     ['id_entidade'=>6, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>6, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>6, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>6, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>6, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Auditoria
        //     ['id_entidade'=>7, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>7, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Finalidade Grupo de Usuário
        //     ['id_entidade'=>8, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>8, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>8, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>8, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>8, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],

        //     // Agricultor
        //     ['id_entidade'=>9, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>9, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>9, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>9, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>9, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],
        //     ['id_entidade'=>9, 'id_tipo_funcionalidade'=>6, 'ativo'=>1],

        //     // Organização
        //     ['id_entidade'=>10, 'id_tipo_funcionalidade'=>1, 'ativo'=>1],
        //     ['id_entidade'=>10, 'id_tipo_funcionalidade'=>2, 'ativo'=>1],
        //     ['id_entidade'=>10, 'id_tipo_funcionalidade'=>3, 'ativo'=>1],
        //     ['id_entidade'=>10, 'id_tipo_funcionalidade'=>4, 'ativo'=>1],
        //     ['id_entidade'=>10, 'id_tipo_funcionalidade'=>5, 'ativo'=>1],
        //     ['id_entidade'=>10, 'id_tipo_funcionalidade'=>6, 'ativo'=>1],
        // ]);

        // DB::table('perfil_funcionalidades')->insert([

        //     // ----------Administrador----------
        //     ['id_perfil'=>1, 'id_funcionalidade'=>1, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>2, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>3, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>4, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>5, 'ativo'=>1],// Pessoa

        //     ['id_perfil'=>1, 'id_funcionalidade'=>6, 'ativo'=>1],// Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>7, 'ativo'=>1],// Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>8, 'ativo'=>1],// Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>9, 'ativo'=>1],// Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>10, 'ativo'=>1],// Grupo

        //     ['id_perfil'=>1, 'id_funcionalidade'=>11, 'ativo'=>1],// Programa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>12, 'ativo'=>1],// Programa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>13, 'ativo'=>1],// Programa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>14, 'ativo'=>1],// Programa
        //     ['id_perfil'=>1, 'id_funcionalidade'=>15, 'ativo'=>1],// Programa

        //     ['id_perfil'=>1, 'id_funcionalidade'=>16, 'ativo'=>1],// Importação
        //     ['id_perfil'=>1, 'id_funcionalidade'=>17, 'ativo'=>1],// Importação
        //     ['id_perfil'=>1, 'id_funcionalidade'=>18, 'ativo'=>1],// Importação
        //     ['id_perfil'=>1, 'id_funcionalidade'=>19, 'ativo'=>1],// Importação
        //     ['id_perfil'=>1, 'id_funcionalidade'=>20, 'ativo'=>1],// Importação

        //     ['id_perfil'=>1, 'id_funcionalidade'=>21, 'ativo'=>1],// Usuário
        //     ['id_perfil'=>1, 'id_funcionalidade'=>22, 'ativo'=>1],// Usuário
        //     ['id_perfil'=>1, 'id_funcionalidade'=>23, 'ativo'=>1],// Usuário
        //     ['id_perfil'=>1, 'id_funcionalidade'=>24, 'ativo'=>1],// Usuário
        //     ['id_perfil'=>1, 'id_funcionalidade'=>25, 'ativo'=>1],// Usuário

        //     ['id_perfil'=>1, 'id_funcionalidade'=>26, 'ativo'=>1],// Perfil
        //     ['id_perfil'=>1, 'id_funcionalidade'=>27, 'ativo'=>1],// Perfil
        //     ['id_perfil'=>1, 'id_funcionalidade'=>28, 'ativo'=>1],// Perfil
        //     ['id_perfil'=>1, 'id_funcionalidade'=>29, 'ativo'=>1],// Perfil
        //     ['id_perfil'=>1, 'id_funcionalidade'=>30, 'ativo'=>1],// Perfil

        //     ['id_perfil'=>1, 'id_funcionalidade'=>31, 'ativo'=>1],// Auditoria
        //     ['id_perfil'=>1, 'id_funcionalidade'=>32, 'ativo'=>1],// Auditoria

        //     ['id_perfil'=>1, 'id_funcionalidade'=>33, 'ativo'=>1],// Finalidade Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>34, 'ativo'=>1],// Finalidade Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>35, 'ativo'=>1],// Finalidade Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>36, 'ativo'=>1],// Finalidade Grupo
        //     ['id_perfil'=>1, 'id_funcionalidade'=>37, 'ativo'=>1],// Finalidade Grupo

        //     ['id_perfil'=>1, 'id_funcionalidade'=>38, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>1, 'id_funcionalidade'=>39, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>1, 'id_funcionalidade'=>40, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>1, 'id_funcionalidade'=>41, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>1, 'id_funcionalidade'=>42, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>1, 'id_funcionalidade'=>43, 'ativo'=>1],// Agricultor

        //     ['id_perfil'=>1, 'id_funcionalidade'=>44, 'ativo'=>1],// Organização
        //     ['id_perfil'=>1, 'id_funcionalidade'=>45, 'ativo'=>1],// Organização
        //     ['id_perfil'=>1, 'id_funcionalidade'=>46, 'ativo'=>1],// Organização
        //     ['id_perfil'=>1, 'id_funcionalidade'=>47, 'ativo'=>1],// Organização
        //     ['id_perfil'=>1, 'id_funcionalidade'=>48, 'ativo'=>1],// Organização
        //     ['id_perfil'=>1, 'id_funcionalidade'=>49, 'ativo'=>1],// Organização

        //     // ----------Funcionário----------
        //     ['id_perfil'=>2, 'id_funcionalidade'=>1, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>2, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>3, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>4, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>5, 'ativo'=>1],// Pessoa

        //     ['id_perfil'=>2, 'id_funcionalidade'=>38, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>39, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>40, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>41, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>42, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>43, 'ativo'=>1],// Agricultor

        //     ['id_perfil'=>2, 'id_funcionalidade'=>44, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>45, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>46, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>47, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>48, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>49, 'ativo'=>1],// Organização

        //     // ----------Agricultor----------
        //     ['id_perfil'=>2, 'id_funcionalidade'=>1, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>2, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>3, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>4, 'ativo'=>1],// Pessoa
        //     ['id_perfil'=>2, 'id_funcionalidade'=>5, 'ativo'=>1],// Pessoa

        //     ['id_perfil'=>2, 'id_funcionalidade'=>38, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>39, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>40, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>41, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>42, 'ativo'=>1],// Agricultor
        //     ['id_perfil'=>2, 'id_funcionalidade'=>43, 'ativo'=>1],// Agricultor

        //     ['id_perfil'=>2, 'id_funcionalidade'=>44, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>45, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>46, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>47, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>48, 'ativo'=>1],// Organização
        //     ['id_perfil'=>2, 'id_funcionalidade'=>49, 'ativo'=>1],// Organização
        // ]);
    }
}
