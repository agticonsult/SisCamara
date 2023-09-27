<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VereadorVotacao extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'ordem', 'votou', 'votouEm', 'id_vereador', 'id_votacao', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'vereador_votacaos';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function vereador()
    {
        return $this->belongsTo(AgentePolitico::class, 'id_vereador');
    }
    public function votacao()
    {
        return $this->belongsTo(VotacaoEletronica::class, 'id_votacao');
    }
}

