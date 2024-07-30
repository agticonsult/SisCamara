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
        'ordem', 'votou', 'voto', 'votouEm', 'id_vereador', 'id_votacao',
        'votacaoAutorizada', 'autorizadaPorUsuario', 'autorizadaEm',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'vereador_votacaos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }

    public function autorizadaPor()
    {
        return $this->belongsTo(User::class, 'autorizadaPorUsuario');
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

