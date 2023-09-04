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

    public function buscar($filtro = null)
    {

        $resultados = $this->where(function($query) use($filtro){

            if(empty(collect($filtro)->except('_token', '_method') === null)){
                $query->get();
            }

            if(isset($filtro['palavra'])){
                $query->where('titulo', 'ILIKE', '%' . $filtro['palavra'] . '%');
            }

            if(isset($filtro['exclusao'])){
                $query->where('titulo', 'NOT ILIKE', '%' . $filtro['exclusao'] . '%');
            }

            if(isset($filtro['id_classificacao'])){
                $query->where('id_classificacao', '=', $filtro['id_classificacao']);
            }

            if(isset($filtro['ano'])){
                $query->where('ano', 'ILIKE', '%' . $filtro['ano'] . '%');
            }

            if(isset($filtro['numero'])){
                $query->where('numero', 'ILIKE', '%' . $filtro['numero'] . '%');
            }

            if(isset($filtro['id_tipo_ato'])){
                $query->where('id_tipo_ato', '=', $filtro['id_tipo_ato']);
            }

            if(isset($filtro['id_assunto'])){
                $query->where('id_assunto', '=', $filtro['id_assunto']);
            }

            if(isset($filtro['altera_dispositivo'])){
                $query->where('altera_dispositivo', '=', $filtro['altera_dispositivo']);
            }

            if(isset($filtro['id_orgao'])){
                $query->where('id_orgao', '=', $filtro['id_orgao']);
            }

            if(isset($filtro['id_forma_publicacao'])){
                $query->where('id_forma_publicacao', '=', $filtro['id_forma_publicacao']);
            }

        })->where(function($subquery) use($filtro){

            if(isset($filtro['data_publicacao'])){
                $subquery->where('data_publicacao', 'ILIKE', $filtro['data_publicacao'] . '%');
            }

        })
        ->paginate(20);

        return $resultados;
    }
}


