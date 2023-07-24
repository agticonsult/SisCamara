<?php
namespace App\Services;

class ValidadorCPFService {

    public static function ehValido($cpf) {

        $cpf_numeros = ValidadorCPFService::removeFormatacao($cpf);

        if(!ValidadorCPFService::verificarNumerosIguais($cpf_numeros)) return false;

        if(!ValidadorCPFService::validarDigitos($cpf_numeros)) return false;

        return true;
    }


    private static function removeFormatacao($cpf) {
        $somente_numeros = preg_replace('/[^0-9]/', '', $cpf);
        return $somente_numeros;

    }

    private static function verificarNumerosIguais($cpf) {
        for($i = 0; $i <= 11; $i++) {
            if(str_repeat($i, 11) == $cpf) return false;
        }

        return true;
    }

    private static function validarDigitos($cpf) {

        $primeiro_digito = 0;
        $segundo_digito = 0;

        for($i = 0, $peso = 10; $i <= 8; $i++, $peso--) {
            $primeiro_digito += $cpf[$i] * $peso;
        }

        for($i = 0, $peso = 11; $i <= 9; $i++, $peso--) {
            $segundo_digito += $cpf[$i] * $peso;
        }

        $calculo_um = (($primeiro_digito % 11) < 2) ? 0 : 11 - ($primeiro_digito % 11);
        $calculo_dois = (($segundo_digito % 11) < 2) ? 0 : 11 - ($segundo_digito % 11);

        if($calculo_um <> $cpf[9] || $calculo_dois  <> $cpf[10]) return false;

        return true;
    }
}
