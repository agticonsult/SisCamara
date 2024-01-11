<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarUserRequest;
use App\Mail\ConfirmacaoEmail;
use App\Models\ComposicaoFamiliar;
use App\Models\Email;
use App\Models\ErrorLog;
use App\Models\Municipio;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\User;
use App\Services\EmailService;
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
    public function registrar()
    {
        try {
            return view('auth.registrar');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "RegistrarController";
            $erro->funcao = "registrar";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //cadastro de novo usuário para logar no sistema
    public function registrarStore(RegistrarUserRequest $request)
    {
        try {
            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
            }

            $novaPessoa = Pessoa::create($request->validated() + [
                'pessoaJuridica' => 0,
            ]);

            $id_pessoa = $novaPessoa->id;

            $novoUsuario = User::create($request->validated() + [
                'id_pessoa' => $id_pessoa,
                'bloqueadoPorTentativa' => 0,
                'confirmacao_email' => 0,
            ]);

            PerfilUser::create([
                'id_user' => $novoUsuario->id,
                'id_tipo_perfil' => 3, //usuário externo
                'cadastradoPorUsuario' => $novoUsuario->id,
            ]);

            Permissao::create([
                'id_user' => $novoUsuario->id,
                'id_perfil' => 3,
                'cadastradoPorUsuario' => $novoUsuario->id,
                // 'ativo' => Permissao::ATIVO //padrão diretamente da migration
            ]);

            //gera um link temporário e criptogrado
            $link = URL::temporarySignedRoute('confirmacao_email', now()->addMinutes(20), [Crypt::encrypt($novoUsuario->id)]);

            $details = [
                'assunto' => 'Confirmação de email',
                'body' => 'Segue abaixo o link',
                'cliente' => $novoUsuario->pessoa->nome,
                'link' => $link,
            ];

            EmailService::configuracaoEmail();

            EmailService::novoEmail($novoUsuario, $link);

            Mail::to($novoUsuario->email)->send(new ConfirmacaoEmail($details));

            return redirect()->route('login')->with('success', 'Cadastro realizado com sucesso! Encaminhado link de confirmação no seu email, cheque sua caixa de spam.');

        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "RegistrarController";
            $erro->funcao = "registrarStore";
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
