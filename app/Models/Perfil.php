<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Perfil extends Model implements Auditable
{
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'descricao', 'cadastradoPorUsuario', 'alteradoPorUsuario',
        'inativadoPorUsuario', 'dataInativado', 'motivoInativado', 'ativo'
    ];

    protected $guarded = ['id', 'created_at', 'update_at'];

    protected $table = 'perfils';

    const ATIVO = 1;
    const INATIVO = 0;

    public function cad_usuario()
    {
        return $this->belongsTo(User::class, 'cadastradoPorUsuario');
    }
    public function funcionalidades_ativas(){
        // return PerfilFuncionalidade::where('id_perfil', '=', $this->id)->where('ativo', '=', 1)->get();
        return $this->hasMany(PerfilFuncionalidade::class, 'id_perfil', 'id')->where('ativo', '=', 1);
    }
    public function funcionalidades(){
        return $this->hasMany(PerfilFuncionalidade::class, 'id_perfil', 'id')->orderBy('ativo', 'desc');
    }
    public function temFuncionalidade(Funcionalidade $funcionalidade)
    {
        $resposta = array();

        $tem = PerfilFuncionalidade::where('id_perfil', '=', $this->id)
            ->where('id_funcionalidade', '=', $funcionalidade->id)
            ->where('ativo', '=', 1)
            ->first();

        if (!$tem){
            array_push($resposta, false);
            array_push($resposta, 0);
            return $resposta;
        }

        array_push($resposta, true);
        array_push($resposta, $tem->perfil->id_abrangencia);
        return $resposta;
    }
}

