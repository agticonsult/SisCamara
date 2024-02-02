<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ModeloDocumento extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'assunto', 'conteudo', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];
    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'modelo_documentos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }

    public static function retornaModelosAtivos()
    {
        return ModeloDocumento::where('ativo', '=', ModeloDocumento::ATIVO)->get();
    }
    public static function retornaModeloAtivo($id)
    {
        return ModeloDocumento::where('id', '=', $id)->where('ativo', '=', ModeloDocumento::ATIVO)->first();
    }
}
