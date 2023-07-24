<?php
namespace App\Services;

class ValidadorCoordenadaService {

    public static function ehValido($lat, $long) {

        // Expressão regular para verificar se os valores são coordenadas decimais
        $pattern = '/^[-]?(\d{1,2}([.]\d+)?|100([.][0]+)?)$/';

        // Verificar se os valores são números decimais válidos e estão dentro do intervalo aceitável
        if (preg_match($pattern, $lat) && preg_match($pattern, $long)) {
            $lat = (float) $lat;
            $long = (float) $long;

            // Verificar se as coordenadas estão dentro dos intervalos permitidos
            if ($lat >= -90 && $lat <= 90 && $long >= -180 && $long <= 180) {
                return true;
            }
        }

        return false;
    }

}
