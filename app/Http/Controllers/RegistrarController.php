<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarUserRequest;
use App\Models\Grupo;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\User;
use App\Services\ErrorLogService;
use RealRashid\SweetAlert\Facades\Alert;

class RegistrarController extends Controller
{

    public function registrar()
    {
        try {
            return view('auth.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'RegistrarController', 'registrarPessoaFisica');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //cadastro de novo usuário PF
    public function store(RegistrarUserRequest $request)
    {
        try {
            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                Alert::toast('Senhas não conferem.','error');
                return redirect()->back();
            }

            $novaPessoa = Pessoa::create($request->validated() + [
                'pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA,
            ]);

            $novoUsuario = User::create($request->validated() + [
                'id_pessoa' => $novaPessoa->id,
                'id_grupo' => Grupo::EXTERNO,
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'confirmacao_email' => User::EMAIL_NAO_CONFIRMADO,
                'cadastroAprovado' => User::USUARIO_REPROVADO,
            ]);

            PerfilUser::create([
                'id_user' => $novoUsuario->id,
                'id_tipo_perfil' => Perfil::USUARIO_EXTERNO,
                'cadastradoPorUsuario' => $novoUsuario->id,
            ]);

            Permissao::create([
                'id_user' => $novoUsuario->id,
                'id_perfil' => Perfil::USUARIO_EXTERNO,
                'cadastradoPorUsuario' => $novoUsuario->id,
            ]);

            // manda e-mail de confirmação através do UserObserver na pasta Observers, configurado no App\Providers\EventServiceProvider
            Alert::toast('Cadastro realizado com sucesso! Encaminhado link de confirmação no seu email, cheque sua caixa de spam.','success');
            return redirect()->route('login');

        }
        catch(\Exception $ex){
            ErrorLogService::salvarPublico($ex->getMessage(), 'RegistrarController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
