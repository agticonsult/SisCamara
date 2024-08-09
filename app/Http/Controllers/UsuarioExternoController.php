<?php

namespace App\Http\Controllers;

use App\Models\Filesize;
use App\Services\ErrorLogService;
use RealRashid\SweetAlert\Facades\Alert;

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
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
