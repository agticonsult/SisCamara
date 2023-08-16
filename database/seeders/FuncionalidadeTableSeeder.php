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
            ['descricao'=>'Ato', 'nomeEntidade'=>'Ato', 'ativo'=>1],
            ['descricao'=>'AssuntoAto', 'nomeEntidade'=>'AssuntoAto', 'ativo'=>1],
            ['descricao'=>'Grupo de Usuário', 'nomeEntidade'=>'Grupo', 'ativo'=>1],
            ['descricao'=>'Usuário', 'nomeEntidade'=>'User', 'ativo'=>1],
            ['descricao'=>'Perfil', 'nomeEntidade'=>'Perfil', 'ativo'=>1],
            ['descricao'=>'Auditoria', 'nomeEntidade'=>'Auditoria', 'ativo'=>1],
            ['descricao'=>'Finalidade Grupo de Usuário', 'nomeEntidade'=>'FinalidadeGrupo', 'ativo'=>1],
            ['descricao'=>'Filesize', 'nomeEntidade'=>'Filesize', 'ativo'=>1],
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
    }
}
