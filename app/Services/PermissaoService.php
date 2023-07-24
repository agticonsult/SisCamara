<?php

namespace App\Services;

use App\Models\Entidade;
use App\Models\Funcionalidade;
use App\Models\PerfilFuncionalidade;
use App\Models\Permissao;
use App\Models\TipoFuncionalidade;
use App\Models\User;
use Carbon\Carbon;

class PermissaoService
{
    public static function temPermissao($id_user, $entidade, $tipoFuncionalidade)
    {
        $e = Entidade::where('nomeEntidade', '=', $entidade)->first();
        $tp = TipoFuncionalidade::where('descricao', '=', $tipoFuncionalidade)->first();

        if (!$e || !$tp){
            return false;
        }

        $funcionalidade = Funcionalidade::where('id_entidade', '=', $e->id)->where('id_tipo_funcionalidade', '=', $tp->id)->where('ativo', '=', 1)->first();

        if (!$funcionalidade){
            return false;
        }

        $permissoes = Permissao::where('id_user', '=', $id_user)->where('ativo', '=', 1)->get();

        foreach ($permissoes as $permissao){

            $tem = $permissao->perfil->temFuncionalidade($funcionalidade);

            if ($tem == true){
                return true;
            }
        }

        return false;
    }

}
