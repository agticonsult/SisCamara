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
        'ordem', 'sub_ordem', 'texto', 'alterado', 'id_proposicao', 'id_ato_add', 'id_tipo_linha',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'linha_atos';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function proposicao()
    {
        return $this->belongsTo(Proposicao::class, 'id_proposicao');
    }
    // public function ato_add()
    // {
    //     return $this->belongsTo(Ato::class, 'id_ato_add');
    // }
    // public function tipo_linha()
    // {
    //     return $this->belongsTo(TipoLinhaAto::class, 'id_tipo_linha');
    // }
}
