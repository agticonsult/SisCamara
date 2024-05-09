<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class GestaoAdministrativa extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'aprovacaoCadastro', 'recebimentoDocumento', 'id_departamento', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'gestao_administrativas';

    const ATIVO = 1;
    const INATIVO = 0;
    const APROVACAO_CADASTRO = 1;
    const RECEBIMENTO_DOCUMENTO = 1;

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
}
