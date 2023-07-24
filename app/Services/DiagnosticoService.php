<?php

namespace App\Services;

use App\Models\Agricultor;
use App\Models\Entrevista;

class DiagnosticoService
{

    /*
        1- Tem pelo menos 1 entrevista cadastrada?

        Não, então cadastrar entrevista.

        Sim, então siga adiante.

        2- Tem entrevista não finalizada?

        Não, então poderá apenas visualizar os dados

        Sim, pode cadastrar/alterar.

        -----------------------------------------------------------------

        3- Quem pode preencher a ficha?

        Funcionário do IDR
        Agricultor Titular
        Familiares do titular
        Membros da organização
    */

    public static function entrevista($id_cliente)
    {
        $resposta = array();

        $agricultor = Agricultor::where('id_user', '=', $id_cliente)->where('ativo', '=', 1)->first();

        $titular = 0;
        if ($agricultor){
            if ($agricultor->titular == true){
                $titular = 1;
            }
        }

        $entrevista = Entrevista::where('id_cliente', '=', $id_cliente)->where('ativo', '=', 1)->orderBy('created_at', 'desc')->first();

        $resposta = [
            'agricultor' => $agricultor,
            'titular' => $titular,
            'entrevista' => $entrevista
        ];

        return $resposta;
    }
}
