<?php

namespace App\Http\Controllers;

use App\Services\ErrorLogService;
use App\Utils\UploadFotoUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FotoPerfilController extends Controller
{

    public function store(Request $request)
    {
        try {
            if ($request->fImage) {
                UploadFotoUtil::identificadorFileUpload($request, Auth::user());
                return redirect()->back()->with('success', 'Foto de perfil alterado com sucesso!');
            }
            return redirect()->back()->with('erro', 'Selecione uma imagem.');
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FotoPerfilController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
