<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class HistoricoMovimentacaoDoc extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'dataEncaminhado', 'dataAprovado', 'dataReprovado', 'aprovadoPor' ,'reprovadoPor', 'id_status', 'id_documento', 'cadastradoPorUsuario', 'alteradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'historico_movimentacao_docs';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function status()
    {
        return $this->belongsTo(StatusDepartamentoDocumento::class, 'id_status');
    }
    // public function departamento()
    // {
    //     return $this->belongsTo(Departamento::class, 'id_departamento_encaminhado');
    // }
    public function documento()
    {
        return $this->belongsTo(DepartamentoDocumento::class, 'id_documento');
    }

    public static function retornaHistoricoMovAtivo($id)
    {
        return HistoricoMovimentacaoDoc::where('id_documento', '=', $id)->where('ativo', '=', HistoricoMovimentacaoDoc::ATIVO)->first();
    }

}
