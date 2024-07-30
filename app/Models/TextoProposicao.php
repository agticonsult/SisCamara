<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TextoProposicao extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'ordem', 'sub_ordem', 'texto', 'alterado', 'id_proposicao',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'texto_proposicaos';

    const ATIVO = 1;
    const INATIVO = 0;

    const TEXTO_ALTERADO = 1;
    const TEXTO_NAO_ALTERADO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    
    public function proposicao()
    {
        return $this->belongsTo(Proposicao::class, 'id_proposicao');
    }
}
