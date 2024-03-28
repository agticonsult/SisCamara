<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AnexoHistoricoMovimentacao extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'nome_original', 'nome_hash', 'diretorio', 'id_movimentacao', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'anexo_historico_movimentacaos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function movimentacao()
    {
        return $this->belongsTo(HistoricoMovimentacaoDoc::class, 'id_movimentacao');
    }
}
