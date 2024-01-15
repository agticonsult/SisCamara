<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Funcionalidade extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'id_entidade', 'id_tipo_funcionalidade', 'cadastradoPorUsuario', 'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'funcionalidades';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function tipo_funcionalidade()
    {
        return $this->belongsTo(TipoFuncionalidade::class, 'id_tipo_funcionalidade');
    }
    public function entidade()
    {
        return $this->belongsTo(Entidade::class, 'id_entidade');
    }
    public function ehFuncionalidadeDoPerfil($id_perfil)
    {
        $eh = PerfilFuncionalidade::where('id_perfil', '=', $id_perfil)->where('id_funcionalidade', '=', $this->id)->where('ativo', '=', 1)->first();

        if (!$eh){
            return false;
        }
        return true;
    }
}

