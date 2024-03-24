<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Documento extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'titulo', 'conteudo', 'protocolo', 'id_tipo_documento', 'id_tipo_workflow', 'reprovado_em_tramitacao', 'finalizado',
        'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'documentos';

    const ATIVO = 1;
    const INATIVO = 0;
    const APROVACAO_DOC = 1;
    const REPROVACAO_DOC = 2;
    const CRIACAO_DOC = 3;
    const FINALIZACAO_DOC = 4;
    const ATUALIZACAO_DOC = 5;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
    public function tipoWorkflow()
    {
        return $this->belongsTo(TipoWorkflow::class, 'id_tipo_workflow');
    }

    public function reprovacao()
    {
        return HistoricoMovimentacaoDoc::where('id_documento', $this->id)
            ->where('ativo', HistoricoMovimentacaoDoc::ATIVO)
            ->where('id_status', 2)
            ->latest()
            ->first();
    }

    public function finalizacao()
    {
        return HistoricoMovimentacaoDoc::where('id_documento', $this->id)
            ->where('ativo', HistoricoMovimentacaoDoc::ATIVO)
            ->where('id_status', 4)
            ->lastest()
            ->first();
    }

    public static function retornaDocumentosDepAtivos()
    {
        return Documento::where('ativo', '=', Documento::ATIVO)->get();
    }
    public static function retornaDocumentoAtivo($id)
    {
        return Documento::where('id', '=', $id)->where('ativo', '=', Documento::ATIVO)->first();
    }

    // retorna itens da tabela AuxiliarDocumentoDepartamento que correspondem ao documento
    public function tramitacao()
    {
        return $this->hasMany(AuxiliarDocumentoDepartamento::class, 'id_documento', 'id')->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)->orderBy('ordem');
    }

    // retorna o item da tabela AuxiliarDocumentoDepartamento do departamento atual
    public function dep_atual()
    {
        return AuxiliarDocumentoDepartamento::where('id_documento', $this->id)->where('atual', 1)->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)->first();
    }

    // retorna o item da tabela AuxiliarDocumentoDepartamento do próximo departamento
    public function proximo_dep()
    {
        if ($this->dep_atual() != null) {
            return AuxiliarDocumentoDepartamento::where('id_documento', $this->id)->where('ordem', ($this->dep_atual()->ordem + 1))
                ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                ->first();
        }else {
            return null;
        }
    }

    // retorna o item da tabela AuxiliarDocumentoDepartamento do departamento anterior
    public function dep_anterior()
    {
        if ($this->dep_atual() != null) {
            return AuxiliarDocumentoDepartamento::where('id_documento', $this->id)->where('ordem', ($this->dep_atual()->ordem - 1))
                ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
                ->first();
        }else {
            return null;
        }
    }

    public function podeTramitar($id_user) {
        if ($this->dep_atual() == null) {
            return false;
        }

        $departamento = Departamento::find($this->dep_atual()->id_departamento);

        foreach ($departamento->usuarios as $user) {
            if ($user->id == $id_user) {
                return true;
            }
        }

        return false;
    }
}
