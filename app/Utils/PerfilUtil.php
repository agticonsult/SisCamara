<?php

namespace App\Utils;

use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;

class PerfilUtil
{
    public static function associarPerfis(array $idPerfils, $novoUsuario)
    {
        foreach ($idPerfils as $idPerfil) {
            $perfil = Perfil::where('id', $idPerfil)->where('ativo', Perfil::ATIVO)->first();

            if ($perfil) {
                self::criarPerfilUser($novoUsuario, $idPerfil);
                self::criarPermissao($novoUsuario, $idPerfil);
            }
        }
    }

    private static function criarPerfilUser($novoUsuario, $idPerfil)
    {
        PerfilUser::create([
            'id_user' => $novoUsuario->id,
            'id_tipo_perfil' => $idPerfil,
            'cadastradoPorUsuario' => $novoUsuario->id,
        ]);
    }

    private static function criarPermissao($novoUsuario, $idPerfil)
    {
        Permissao::create([
            'id_user' => $novoUsuario->id,
            'id_perfil' => $idPerfil,
            'cadastradoPorUsuario' => $novoUsuario->id,
        ]);
    }

}
