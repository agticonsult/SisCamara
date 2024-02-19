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
    public function alt_usuario()
    {
        return $this->belongsTo(User::class, 'alteradoPorUsuario');
    }
    public function status()
    {
        return $this->belongsTo(StatusDepartamentoDocumento::class, 'id_status');
    }
    // public function departamento()
    // {
    //     return $this->hasMany(Departamento::class, 'id_departamento_encaminhado')->where('ativo', '=', Departamento::ATIVO);
    // }
    public function documento()
    {
        return $this->belongsTo(DepartamentoDocumento::class, 'id_documento');
    }

    public static function retornaHistoricoMovAtivo($id)
    {
        return HistoricoMovimentacaoDoc::where('id_documento', '=', $id)->where('ativo', '=', HistoricoMovimentacaoDoc::ATIVO)->get();
    }
    public static function retornaUltimoHistoricoMovStatusAtivo($id)
    {
        return HistoricoMovimentacaoDoc::where('id_documento', '=', $id)->where('ativo', '=', HistoricoMovimentacaoDoc::ATIVO)->latest()->first();
    }

}
