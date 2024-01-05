<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Ato extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'titulo', 'ano', 'numero', 'subtitulo', 'data_publicacao', 'altera_dispositivo', 'id_orgao', 'id_classificacao', 'id_forma_publicacao', 'id_assunto', 'id_grupo', 'id_tipo_ato', 'cadastradoPorUsuario',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'atos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function orgao()
    {
        return $this->belongsTo(OrgaoAto::class ,'id_orgao');
    }
    public function classificacao()
    {
        return $this->belongsTo(ClassificacaoAto::class ,'id_classificacao');
    }
    public function forma_publicacao()
    {
        return $this->belongsTo(FormaPublicacaoAto::class ,'id_forma_publicacao');
    }
    public function assunto()
    {
        return $this->belongsTo(AssuntoAto::class ,'id_assunto');
    }
    public function grupo()
    {
        return $this->belongsTo(Grupo::class ,'id_grupo');
    }
    public function tipo_ato()
    {
        return $this->belongsTo(TipoAto::class ,'id_tipo_ato');
    }
    public function linhas_originais_ativas()
    {
        $linhas = LinhaAto::where('id_ato_principal', '=', $this->id)->where('id_tipo_linha', '=', 1)->where('ativo', '=', 1)->orderBy('ordem')->get();
        return $linhas;
        // return $this->hasMany(LinhaAto::class, 'id_ato', 'id')->where('id_tipo_linha', '=', 1)->where('ativo', '=', 1);
    }
    public function todas_linhas_ativas()
    {
        $linhas = LinhaAto::where('id_ato_principal', '=', $this->id)->where('ativo', '=', 1)->orderBy('ordem')->get();
        return $linhas;
        // return $this->hasMany(LinhaAto::class, 'id_ato', 'id')->where('ativo', '=', 1);
    }
    public function linhas_inalteradas_ativas()
    {
        $linhas = LinhaAto::where('id_ato_principal', '=', $this->id)->where('alterado', '=', 0)->where('ativo', '=', 1)->orderBy('ordem', 'asc')->orderBy('sub_ordem', 'asc')->get();
        return $linhas;
        // return $this->hasMany(LinhaAto::class, 'id_ato', 'id')->where('ativo', '=', 1);
    }
    public function anexos_ativos()
    {
        $anexos = AnexoAto::where('id_ato', '=', $this->id)->where('ativo', '=', 1)->get();
        return $anexos;
        // return $this->hasMany(LinhaAto::class, 'id_ato', 'id')->where('ativo', '=', 1);
    }
    public function atos_relacionados_ativos()
    {
        $atos = AtoRelacionado::where('id_ato_principal', '=', $this->id)->where('ativo', '=', 1)->get();
        return $atos;
        // return $this->hasMany(LinhaAto::class, 'id_ato', 'id')->where('ativo', '=', 1);
    }

    public function linha_atos()
    {
        return $this->hasMany(LinhaAto::class, 'id_ato_principal', 'id');
    }

    public function buscar($filtro = null)
    {
        $resultados = $this->leftJoin('assunto_atos', 'atos.id_assunto', '=', 'assunto_atos.id')
        ->leftJoin('tipo_atos', 'atos.id_tipo_ato', '=', 'tipo_atos.id')
        ->leftJoin('orgao_atos', 'atos.id_orgao', '=', 'orgao_atos.id')
        ->leftJoin('forma_publicacao_atos', 'atos.id_forma_publicacao', '=', 'forma_publicacao_atos.id')
        ->where(function($query) use($filtro){

            if(empty(collect($filtro)->except('_token', '_method') === null)){
                $query->get();
            }

            if(isset($filtro['id_classificacao'])){
                $query->where('atos.id_classificacao', '=', $filtro['id_classificacao']);
            }

            if(isset($filtro['ano'])){
                $query->where('atos.ano', '=', $filtro['ano']);
            }

            if(isset($filtro['numero'])){
                $query->where('atos.numero', 'LIKE', '%'.$filtro['numero'].'%');
            }

            if(isset($filtro['id_tipo_ato'])){
                $query->where('atos.id_tipo_ato', '=', $filtro['id_tipo_ato']);
            }

            if(isset($filtro['id_assunto'])){
                $query->where('atos.id_assunto', '=', $filtro['id_assunto']);
            }

            if(isset($filtro['altera_dispositivo'])){
                $query->where('atos.altera_dispositivo', '=', $filtro['altera_dispositivo']);
            }

            if(isset($filtro['id_orgao'])){
                $query->where('atos.id_orgao', '=', $filtro['id_orgao']);
            }

            if(isset($filtro['id_forma_publicacao'])){
                $query->where('atos.id_forma_publicacao', '=', $filtro['id_forma_publicacao']);
            }

        })->where(function($subquery) use($filtro){

            if(isset($filtro['data_publicacao'])){
                $subquery->where('atos.data_publicacao', 'LIKE', '%'.$filtro['data_publicacao'] . '%');
            }

        })
        ->where('atos.ativo', '=', 1)
        ->select(
            'atos.*', 'assunto_atos.descricao as assunto', 'tipo_atos.descricao as tipo_ato',
            'orgao_atos.descricao as orgao', 'forma_publicacao_atos.descricao as forma_publicacao',
        )
        ->orderBy("atos.titulo", "asc")->paginate(20);

        return $resultados;
    }

    // public function buscar($filtro = null)
    // {
    //     return $this->select('atos.*', 'linha_atos.texto')
    //     ->from('atos')
    //     ->leftJoin('linha_atos', 'atos.id', '=', 'linha_atos.id_ato_principal')
    //     ->where(function($query) use($filtro){

    //         if(isset($filtro['id_classificacao'])){
    //             $query->where('id_classificacao', '=', $filtro['id_classificacao']);
    //         }

    //         if(isset($filtro['ano'])){
    //             $query->where('ano', '=', $filtro['ano']);
    //         }

    //         if(isset($filtro['numero'])){
    //             $query->where('numero', 'LIKE', '%' . $filtro['numero'] . '%');
    //         }

    //         if(isset($filtro['id_tipo_ato'])){
    //             $query->where('id_tipo_ato', '=', $filtro['id_tipo_ato']);
    //         }

    //         if(isset($filtro['id_assunto'])){
    //             $query->where('id_assunto', '=', $filtro['id_assunto']);
    //         }

    //         if(isset($filtro['altera_dispositivo'])){
    //             $query->where('altera_dispositivo', '=', $filtro['altera_dispositivo']);
    //         }

    //         if(isset($filtro['id_orgao'])){
    //             $query->where('id_orgao', '=', $filtro['id_orgao']);
    //         }

    //         if(isset($filtro['id_forma_publicacao'])){
    //             $query->where('id_forma_publicacao', '=', $filtro['id_forma_publicacao']);
    //         }

    //     })->when(isset($filtro['data_publicacao']), function($query) use ($filtro){
    //         $query->whereDate('data_publicacao', $filtro['data_publicacao']);
    //     })
    //     ->paginate(20);
    // }
}


