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
        'titulo', 'conteudo', 'protocolo', 'id_tipo_documento', 'id_tipo_workflow','cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'departamento_documentos';

    const ATIVO = 1;
    const INATIVO = 0;
    const CRIACAO_DOC = 3;

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

    public static function retornaDocumentosDepAtivos()
    {
        return DepartamentoDocumento::where('ativo', '=', DepartamentoDocumento::ATIVO)->get();
    }
    public static function retornaDocumentoDepAtivo($id)
    {
        return DepartamentoDocumento::where('id', '=', $id)->where('ativo', '=', DepartamentoDocumento::ATIVO)->first();
    }

    // retorna itens da tabela AuxiliarDocumentoDepartamento que correspondem ao documento
    public function tramitacao()
    {
        return $this->hasMany(AuxiliarDocumentoDepartamento::class, 'id_documento', 'id')->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)->orderBy('ordem');
    }

    // retorna o item da tabela AuxiliarDocumentoDepartamento do departamento atual
    public function departamento_atual()
    {
        return AuxiliarDocumentoDepartamento::where('id_documento', $this->id)->where('atual', 1)->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)->first();
    }

    // retorna o item da tabela AuxiliarDocumentoDepartamento do próximo departamento
    public function proximo_dep()
    {
        return AuxiliarDocumentoDepartamento::where('id_documento', $this->id)->where('ordem', ($this->departamento_atual()->ordem + 1))
            ->where('ativo', AuxiliarDocumentoDepartamento::ATIVO)
            ->first();
    }
}
