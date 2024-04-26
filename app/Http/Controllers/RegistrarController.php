<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarUserPJRequest;
use App\Http\Requests\RegistrarUserRequest;
use App\Mail\ConfirmacaoEmail;
use App\Models\ComposicaoFamiliar;
use App\Models\Email;
use App\Models\ErrorLog;
use App\Models\Municipio;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\User;
use App\Services\EmailService;
use App\Services\ErrorLogService;
use App\Services\ValidadorCPFService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

use function GuzzleHttp\Promise\all;

class RegistrarController extends Controller
{
    public function selecionarPessoa()
    {
        try {
            return view('auth.cadastro.selecionarCadastro');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'RegistrarController', 'selecionarPessoa');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function registrarPessoaFisica()
    {
        try {
            return view('auth.cadastro.pessoa-fisica.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'RegistrarController', 'registrarPessoaFisica');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function registrarPessoaJuridica()
    {
        try {
            return view('auth.cadastro.pessoa-juridica.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'RegistrarController', 'registrarPessoaJuridica');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //cadastro de novo usuário PF
    public function pessoaFisicaStore(RegistrarUserRequest $request)
    {
        try {
            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
            }

            $novaPessoa = Pessoa::create($request->validated() + [
                'pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA,
            ]);

            $novoUsuario = User::create($request->validated() + [
                'id_pessoa' => $novaPessoa->id,
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'confirmacao_email' => User::EMAIL_CONFIRMADO,
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

            return redirect()->route('login')->with('success', 'Cadastro realizado com sucesso! Encaminhado link de confirmação no seu email, cheque sua caixa de spam.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvarPublico($ex->getMessage(), 'RegistrarController', 'pessoaFisicaStore');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    //cadastro de novo usuário PJ
    public function pessoaJuridicaStore(RegistrarUserPJRequest $request)
    {
        try {
            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
            }

            $novaPessoa = Pessoa::create($request->validated() + [
                'pessoaJuridica' => Pessoa::PESSOA_JURIDICA,
            ]);

            $novoUsuario = User::create($request->validated() + [
                'id_pessoa' => $novaPessoa->id,
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'confirmacao_email' => User::EMAIL_CONFIRMADO,
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
            return redirect()->route('login')->with('success', 'Cadastro realizado com sucesso! Encaminhado link de confirmação no seu email, cheque sua caixa de spam.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvarPublico($ex->getMessage(), 'RegistrarController', 'pessoaJuridicaStore');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
