<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Filesize;
use App\Models\FotoPerfil;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Ramsey\Uuid\Uuid;

class FotoPerfilController extends Controller
{

    public function store(Request $request)
    {
        try {
            //verifica se o arquivo é válido
            if($request->hasFile('fImage') && $request->file('fImage')->isValid()){

                $max_filesize = Filesize::where('id_tipo_filesize', '=', Filesize::FOTO_PERFIL)->where('ativo', '=', Filesize::ATIVO)->first();
                if ($max_filesize){
                    if ($max_filesize->mb != null){
                        if (is_int($max_filesize->mb)){
                            $mb = $max_filesize->mb;
                        }
                        else{
                            $mb = 2;
                        }
                    }
                    else{
                        $mb = 2;
                    }
                }
                else{
                    $mb = 2;
                }

                if (filesize($request->file('fImage')) <= 1048576 * $mb){
                    $nome_original = $request->fImage->getClientOriginalName();
                    $extensao = $request->fImage->extension();

                    //validação de extensão de imagens
                    if(
                        $extensao != 'jpg' &&
                        $extensao != 'jpeg' &&
                        $extensao != 'png'
                    ) {
                        return redirect()->back()->with('erro', 'Extensão de imagem inválida. Extensões permitidas .png, .jpg e .jpeg')->withInput();
                    }

                    $nome_hash = Uuid::uuid4();
                    $datahora = Carbon::now()->timestamp;
                    $nome_hash = $nome_hash . '-' . $datahora . '.' . $extensao;
                    $upload = $request->fImage->storeAs('public/foto-perfil/', $nome_hash);

                    if(!$upload){
                        return redirect()->back()->with('erro', 'Ocorreu um erro ao salvar a foto de perfil.')->withInput();
                    }
                    else{

                        $fotos = FotoPerfil::where('id_user', '=', auth()->user()->id)->where('ativo', '=', FotoPerfil::ATIVO)->get();
                        foreach ($fotos as $foto) {
                            $foto->update([
                                'inativadoPorUsuario' => Auth::user()->id,
                                'dataInativado' => Carbon::now(),
                                'motivoInativado' => "Alteração de foto de perfil pelo usuário",
                                'ativo' => FotoPerfil::INATIVO
                            ]);
                        }

                        FotoPerfil::create([
                            'nome_original' => $nome_original,
                            'nome_hash' => $nome_hash,
                            'id_user' => Auth::user()->id,
                            'cadastradoPorUsuario' => Auth::user()->id
                        ]);
                    }
                }
                else{
                    return redirect()->back()->with('erro', 'Arquivo maior que ' . $mb . 'MB');
                }
            }
            else{
                return redirect()->back()->with('erro', 'Selecione uma imagem.');
            }

            return redirect()->back()->with('success', 'Foto de perfil alterado com sucesso!');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FotoPerfilController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
