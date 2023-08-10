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
        'titulo', 'ano', 'numero', 'subtitulo', 'altera_dispositivo', 'id_assunto', 'id_grupo', 'id_tipo_ato', 'cadastradoPorUsuario',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'atos';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
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
}


