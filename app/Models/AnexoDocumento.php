<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AnexoDocumento extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'nome_original', 'nome_hash', 'id_usuario', 'id_historico_movimentacao'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'anexo_documentos';

    const ATIVO = 1;
    const INATIVO = 0;

}
