<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Proposicao extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'nome', 'assunto', 'conteudo', 'id_localizacao', 'id_status', 'id_modelo', 'cadastradoPorUsuario',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'proposicaos';

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function modelo()
    {
        return $this->belongsTo(ModeloProposicao::class, 'id_modelo');
    }
    public function localizacao()
    {
        return $this->belongsTo(LocalizacaoProposicao::class, 'id_localizacao');
    }
    public function status()
    {
        return $this->belongsTo(StatusProposicao::class, 'id_status');
    }
}
