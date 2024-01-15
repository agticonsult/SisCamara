<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TipoFilesize extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'descricao', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoIntivaado', 'ativo'
    ];
    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'tipo_filesizes';

    const ATIVO = 1;
    const INATIVO = 0;
}
