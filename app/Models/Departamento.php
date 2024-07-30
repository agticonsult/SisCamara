<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Departamento extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'descricao', 'id_coordenador', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoIntivaado', 'ativo'
    ];
    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'departamentos';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }

    public function coordenador()
    {
        return $this->belongsTo(User::class, 'id_coordenador');
    }

    public function usuarios()
    {
        // return $this->belongsToMany(User::class, 'departamento_usuarios', 'id_departamento', 'id_user')->withPivot('cadastradoPorUsuario');
        return $this->belongsToMany(User::class, 'departamento_usuarios', 'id_departamento', 'id_user')->wherePivot('ativo', '=', DepartamentoUsuario::ATIVO);
    }

    public function tipoDocumentos()
    {
        return $this->belongsToMany(TipoDocumento::class, 'departamento_tramitacaos', 'id_departamento', 'id_tipo_documento');
    }

    public function historicoMovimentacao()
    {
        return $this->belongsTo(HistoricoMovimentacaoDoc::class, 'id_departamento');
    }

    public static function retornaDepartamentosAtivos()
    {
        return Departamento::where('ativo', '=', Departamento::ATIVO)->with('usuarios')->get();
    }

    public static function retornaDepartamentoAtivo($id)
    {
        return Departamento::where('id', '=', $id)->where('ativo', '=', Departamento::ATIVO)->first();
    }
    
    public function estaVinculadoGestaoAdm()
    {
        $pertence = GestaoAdministrativa::where('id_departamento', '=', $this->id)->where('ativo', '=', GestaoAdministrativa::ATIVO)->first();

        if (!$pertence) {
            return false;
        }
        return true;
    }
}
