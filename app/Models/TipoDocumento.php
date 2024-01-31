<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TipoDocumento extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'nome', 'tipoDocumento', 'nivel', 'contador', 'cadastradoPorUsuario', 'alteradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'tipo_documentos';

    const ATIVO = 1;
    const INATIVO = 0;
    const NIVEL_INI = 0;

    public static function retornaTipoDocumentosAtivos()
    {
        return TipoDocumento::where('ativo', '=', TipoDocumento::ATIVO)->get();
    }
}
