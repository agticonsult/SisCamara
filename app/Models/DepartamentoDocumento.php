<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DepartamentoDocumento extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'titulo', 'conteudo', 'id_tipo_documento', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'departamento_documentos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
    public static function retornaDocumentosDepAtivos()
    {
        return DepartamentoDocumento::where('ativo', '=', DepartamentoDocumento::ATIVO)->get();
    }
    public static function retornaDocumentoDepAtivo($id)
    {
        return DepartamentoDocumento::where('id', '=', $id)->where('ativo', '=', DepartamentoDocumento::ATIVO)->first();
    }
}
