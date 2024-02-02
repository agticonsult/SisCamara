<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Funcionalidade extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'id_entidade', 'id_tipo_funcionalidade', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'funcionalidades';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function tipo_funcionalidade()
    {
        return $this->belongsTo(TipoFuncionalidade::class, 'id_tipo_funcionalidade');
    }
    public function entidade()
    {
        return $this->belongsTo(Entidade::class, 'id_entidade');
    }
    public function ehFuncionalidadeDoPerfil($id_perfil)
    {
        $eh = PerfilFuncionalidade::where('id_perfil', '=', $id_perfil)->where('id_funcionalidade', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$eh){
            return false;
        }
        return true;
    }

    /*
    ** funcionalidades político, usuários interno e externo
    */
    
    public static function funcionalidadesPolitico()
    {
        $funcionalidadesPolitico = Funcionalidade::leftJoin('entidades', 'entidades.id', '=', 'funcionalidades.id_entidade')
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

        return $funcionalidadesPolitico;
    }

    public static function funcionalidadeUsuarioInterno()
    {
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

        return $funcionalidadesUsuarioInterno;
    }

    public static function funcionalidadeUsuarioExterno()
    {
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

        return $funcionalidadesUsuarioExterno;
    }
}

