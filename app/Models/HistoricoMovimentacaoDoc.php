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
        'parecer', 'id_documento', 'id_usuario', 'id_status', 'id_departamento', 'atualDepartamento', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'historico_movimentacao_docs';

    const ATIVO = 1;
    const INATIVO = 0;
    const ATUAL_DEPARTAMENTO = 1;

    public function status()
    {
        return $this->belongsTo(StatusDocumento::class, 'id_status');
    }
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento')->where('ativo', '=', Departamento::ATIVO);
    }
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    public function obterDepartamentoAtual()
    {
        $historicos = HistoricoMovimentacaoDoc::where('id_documento', $this->id)->get();
    }

    public static function retornaHistoricoMovAtivo($id)
    {
        return HistoricoMovimentacaoDoc::where('id_documento', '=', $id)
            ->where('ativo', '=', HistoricoMovimentacaoDoc::ATIVO)
            ->orderByDesc('created_at')
            ->get();
    }
    public static function retornaUltimoHistoricoMovStatusAtivo($id)
    {
        return HistoricoMovimentacaoDoc::where('id_documento', '=', $id)
            ->where('ativo', '=', HistoricoMovimentacaoDoc::ATIVO)
            ->latest()
            ->first();
    }

}
