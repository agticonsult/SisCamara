<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DepartamentoTramitacao extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'id_tipo_documento', 'id_departamento', 'ordem', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'departamento_tramitacaos';

    const ATIVO = 1;
    const INATIVO = 0;
    const CONTADOR_INI = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
    public function inativadoPor()
    {
        return $this->belongsTo(User::class, 'inativadoPorUsuario');
    }

    public static function retornaProximoDocumento($id)
    {
        return DepartamentoTramitacao::where('id_tipo_documento', '=', $id)->where('ativo', '=', DepartamentoTramitacao::ATIVO)->orderBy('ordem')->first();
    }
    public static function retornaDepartamentoTramitacoes($id)
    {
        return DepartamentoTramitacao::where('id_tipo_documento', '=', $id)->orderBy('ordem')->get();
    }
}
