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
        'nome', 'tipoDocumento', 'nivel', 'cadastradoPorUsuario', 'alteradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'tipo_documentos';

    const ATIVO = 1;
    const INATIVO = 0;
    const NIVEL_INI = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function departamentoVinculados()
    {
        return $this->belongsToMany(Departamento::class, 'departamento_tramitacaos', 'id_tipo_documento', 'id_departamento')->wherePivot('ativo', '=', DepartamentoTramitacao::ATIVO);
    }

    public static function retornaTipoDocumentosAtivos()
    {
        return TipoDocumento::where('ativo', '=', TipoDocumento::ATIVO)->with('departamentoVinculados')->get();
    }
    public static function retornaTipoDocumentoAtivo($id)
    {
        return TipoDocumento::where('id', '=', $id)->where('ativo', '=', TipoDocumento::ATIVO)->with('departamentoVinculados')->first();
    }
}
