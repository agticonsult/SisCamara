<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;

class ErrorLogService
{
    public static function salvar(string $erro, string $controlador, string $funcao)
    {
        ErrorLog::create([
            'erro' => $erro,
            'controlador' => $controlador,
            'funcao' => $funcao,
            'cadastradoPorUsuario' => Auth::user()->id
        ]);
    }


}
