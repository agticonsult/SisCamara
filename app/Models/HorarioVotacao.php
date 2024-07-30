<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class HorarioVotacao extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'horario', 'id_tipo_horario', 'id_votacao', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'horario_votacaos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }

    public function tipo_horario()
    {
        return $this->belongsTo(TipoHorarioVotacao::class, 'id_tipo_horario');
    }
    
    public function votacao()
    {
        return $this->belongsTo(VotacaoEletronica::class, 'id_votacao');
    }
}

