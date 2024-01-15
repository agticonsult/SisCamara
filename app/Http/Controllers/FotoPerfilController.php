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

                $max_filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', Filesize::ATIVO)->first();
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

                    // $nome_hash = Carbon::now()->timestamp;
                    // $nome_hash = $nome_hash.'.'.$extensao;

                    $nome_hash = Uuid::uuid4();
                    $datahora = Carbon::now()->timestamp;
                    $nome_hash = $nome_hash . '-' . $datahora . '.' . $extensao;
                    //diretório onde estará as fotos de perfil

                    $upload = $request->fImage->storeAs('public/foto-perfil/', $nome_hash);
                    // $upload = $request->fImage->move('foto-perfil/', $nome_hash);

                    // $path = public_path() . '/foto-perfil/';
                    if(!$upload){
                        return redirect()->back()->with('erro', 'Ocorreu um erro ao salvar a foto de perfil.')->withInput();
                    }
                    else{

                        $fotos = FotoPerfil::where('id_user', '=', auth()->user()->id)->where('ativo', '=', FotoPerfil::ATIVO)->get();
                        foreach ($fotos as $foto) {
                            $foto->ativo = FotoPerfil::INATIVO;
                            $foto->inativadoPorUsuario = auth()->user()->id;
                            $foto->dataInativado = Carbon::now();
                            $foto->motivoInativado = "Alteração de foto de perfil pelo usuário";
                            $foto->save();
                        }

                        $foto_perfil = new FotoPerfil();
                        $foto_perfil->nome_original = $nome_original;
                        $foto_perfil->nome_hash = $nome_hash;
                        $foto_perfil->id_user = auth()->user()->id;
                        $foto_perfil->cadastradoPorUsuario = auth()->user()->id;
                        $foto_perfil->ativo = FotoPerfil::ATIVO;
                        $foto_perfil->save();
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
