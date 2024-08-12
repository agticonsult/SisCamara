<?php

namespace App\Http\Controllers;

use App\Services\ErrorLogService;
use App\Utils\UploadFotoUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class FotoPerfilController extends Controller
{
    public function store(Request $request)
    {
        try {
            if ($request->fImage) {
                UploadFotoUtil::identificadorFileUpload($request, Auth::user());
                Alert::toast('Foto de perfil alterado com sucesso!', 'success');
                return redirect()->back();
            }
            Alert::toast('Selecione uma imagem.','error');
            return redirect()->back();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FotoPerfilController', 'store');
            Alert::toast($ex->getMessage(),'error');
            return redirect()->back();
        }
    }
}
