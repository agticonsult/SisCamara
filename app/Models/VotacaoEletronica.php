<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VotacaoEletronica extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'data', 'interrupcoes', 'votacaoIniciada', 'dataHoraInicio', 'dataHoraFim', 'id_tipo_votacao', 'id_proposicao',
        'id_legislatura', 'id_status_votacao',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'votacao_eletronicas';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function tipo_votacao()
    {
        return $this->belongsTo(TipoVotacao::class, 'id_tipo_votacao');
    }
    public function proposicao()
    {
        return $this->belongsTo(Proposicao::class, 'id_proposicao');
    }
    public function legislatura()
    {
        return $this->belongsTo(Legislatura::class, 'id_legislatura');
    }
    public function horarios_ativos()
    {
        $horarios = HorarioVotacao::where('id_votacao', '=', $this->id)->where('ativo', '=', 1)->get();
        return $horarios;
    }
    public function vereadores_ativos()
    {
        $vereadores = VereadorVotacao::where('id_votacao', '=', $this->id)->where('ativo', '=', 1)->get();
        return $vereadores;
    }
}
