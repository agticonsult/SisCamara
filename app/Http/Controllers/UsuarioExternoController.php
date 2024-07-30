<?php

namespace App\Http\Controllers;

use App\Services\ErrorLogService;
use Illuminate\Http\Request;

class UsuarioExternoController extends Controller
{
    public function create()
    {
        try {

        }
        catch(\Exception $ex){
            ErrorLogService::salvarPublico($ex->getMessage(), 'UsuarioExternoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
