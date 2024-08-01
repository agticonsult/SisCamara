<?php

namespace App\Http\Controllers;

use App\Http\Requests\HomeUpdateRequest;
use App\Models\DepartamentoUsuario;
use App\Models\Filesize;
use App\Models\FotoPerfil;
use App\Models\Pessoa;
use App\Models\User;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        try{
            $user = User::where('id', '=', Auth::user()->id)
                ->where('ativo', '=', User::ATIVO)
                ->first();

            $departamentos = DepartamentoUsuario::where('id_user', '=', $user->id)->where('ativo', '=', DepartamentoUsuario::ATIVO)->get();
            $foto_perfil = FotoPerfil::where('id_user', '=', auth()->user()->id)->where('ativo', '=', User::ATIVO)->first();
            $filesize = Filesize::where('id_tipo_filesize', '=', Filesize::FOTO_PERFIL)->where('ativo', '=', Filesize::ATIVO)->first();
            $temFoto = 0;

            if ($foto_perfil){
                $existe = Storage::disk('public')->exists('foto-perfil/'.$foto_perfil->nome_hash);
                // $existe = public_path('foto-perfil/'.$foto_perfil->nome_hash);
                if ($existe){
                    $temFoto = 1;
                }
            }

            return view('home.home', compact('user', 'foto_perfil', 'temFoto', 'filesize', 'departamentos'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'HomeController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(HomeUpdateRequest $request, $id)
    {
        try {
            //Busca o usuário no BD
            $user = User::find($id);
            $user->update($request->validated());

            if ($request->password != null){

                //verificar se a senha antiga está correta
                if (!Hash::check($request->senha_antiga, $user->password)){
                    return redirect()->back()->with('erro', 'A senha antiga está incorreta.')->withInput();
                }

                //verifica se a confirmação de senha estão ok
                if($request->password != $request->confirmacao){
                    return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
                }

                $tamanho_senha = strlen($request->password);
                if ($tamanho_senha < 6 || $tamanho_senha > 35){
                    return redirect()->back()->with('erro', 'Senha inválida.')->withInput();
                }

                $user->password = Hash::make($request->password);
            }

            //Dados de Pessoa
            if($user->id_pessoa){
                // $pessoa = Pessoa::where('id', '=', $user->id_pessoa)->first();
                $pessoa = Pessoa::find($user->id_pessoa);
                $pessoa->update($request->validated());
            }
            return redirect()->route('home')->with('success', 'Cadastro alterado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'HomeController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function information()
    {
        try{
            return view('information');
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'HomeController', 'information');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    // public function alterarPerfil(Request $request)
    // {
    //     try {
    //         // validação do parâmetro (id_perfil) recebido na request
    //         if (isset($request->perfil_ativo)){
    //             if (
    //                 $request->perfil_ativo != 1 && $request->perfil_ativo != 2 &&
    //                 $request->perfil_ativo != 3 && $request->perfil_ativo != 4
    //             ){
    //                 return redirect()->route('home')->with('erro', 'Perfil inválido.');
    //             }
    //             $existePerfilAtivo = TipoPerfil::where('id', '=', $request->perfil_ativo)->where('ativo', '=', 1)->first();
    //             if (!$existePerfilAtivo){
    //                 return redirect()->route('home')->with('erro', 'Não autorizado.');
    //             }
    //             // verifica se o usuario realmente possui acesso ao perfil recebido na requisição
    //             $possuiEssePerfil = PerfilUser::where('id_user', '=', auth()->user()->id)
    //                 ->where('id_tipo_perfil', '=', $request->perfil_ativo)
    //                 ->where('ativo', '=', 1)
    //                 ->first();

    //             if (!$possuiEssePerfil){
    //                 return redirect()->route('home')->with('erro', 'Não autorizado.');
    //             }

    //             $user = User::where('id', '=', Auth::user()->id)->first();
    //             $user->id_tipo_perfil = $request->perfil_ativo;
    //             $user->save();
    //         }

    //         return redirect()->route('home');
    //     }
    //     catch(\Exception $ex){
    //         ErrorLogService::salvar($ex->getMessage(), 'HomeController', 'alterarPerfil');
    //         return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
    //     }
    // }

}
