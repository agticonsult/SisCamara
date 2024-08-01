<?php

namespace App\Http\Controllers;

use App\Models\Filesize;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;

class UsuarioExternoController extends Controller
{
    public function create()
    {
        try {
            $filesize = Filesize::where('id_tipo_filesize', '=', Filesize::FOTO_PERFIL)->where('ativo', '=', Filesize::ATIVO)->first();
            return view('usuario-externo.create', compact('filesize'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvarPublico($ex->getMessage(), 'UsuarioExternoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
