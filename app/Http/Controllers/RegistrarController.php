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
            // validação dos campos
            // $input = [
            //     'name' => $request->name,
            //     'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
            //     'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
            //     'email' => $request->email,
            //     'password' => $request->password,
            //     'confirmacao' => $request->confirmacao,
            //     'telefone_celular' => preg_replace('/[^0-9]/', '', $request->telefone_celular),
            //     'telefone_celular2' => preg_replace('/[^0-9]/', '', $request->telefone_celular2)
            // ];
            // $rules = [
            //     'name' => 'required|max:255',
            //     'cpf' => 'required|min:11|max:11',
            //     'email' => 'required|email',
            //     'dt_nascimento_fundacao' => 'required|max:10',
            //     'password' => 'required|min:6|max:35',
            //     'confirmacao' => 'required|min:6|max:35',
            //     'telefone_celular' => 'max:11',
            //     'telefone_celular2' => 'max:11'
            // ];
            // $messages = [
            //     'name.required' => 'O nome é obrigatório.',
            //     'name.max' => 'Nome: Máximo 255 caracteres.',

            //     'cpf.required' => 'O CPF é obrigatório.',
            //     'cpf.max' => 'CPF: Máximo 11 caracteres.',

            //     'email.required' => 'O email é obrigatório.',
            //     'email.max' => 'Email: Máximo 255 caracteres',

            //     'dt_nascimento_fundacao.required' => 'Data nascimento é obrigatório',
            //     'dt_nascimento_fundacao.max' => 'Data nascimento: Máximo 11 caracteres',

            //     'telefone_celular.max' => 'Telefone/Celular: Máximo 11 caracteres',

            //     'telefone_celular2.max' => 'Telefone/Celular recado: Máximo 11 caracteres',

            //     'password.required' => 'A senha é obrigatória.',
            //     'password.min' => 'Senha: Minímo 6 caracteres',
            //     'password.max' => 'Senha: Máximo 35 caracteres',

            //     'confirmacao.required' => 'Confirmação é obrigatória',
            //     'confirmacao.min' => 'Confirmação: Minímo 6 caracteres',
            //     'confirmacao.max' => 'Confirmação: Máximo 35 caracteres',
            // ];

            // $validarUsuario = Validator::make($input, $rules, $messages);
            // $validarUsuario->validate();

            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
            }

            //varifica se já existe um email ativo cadaastrado no BD
            // $verifica_user = User::where('email', '=', $request->email)
            //     ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))
            //     ->select('email', 'cpf')
            //     ->first();

            // $verifica_user = User::where(function (Builder $query) use ($request) {
            //     return
            //         $query->where('email', '=', $request->email)
            //             ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf));
            //         })
            //     ->select('id', 'email', 'cpf')
            //     ->first();


            // //existe um email cadastrado?
            // if($verifica_user){
            //     return redirect()->back()->with('erro', 'Já existe um usuário cadastrado com esse email e/ou CPF.')->withInput();
            // }

            // if(!ValidadorCPFService::ehValido($request->cpf)){
            //     return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
            // }

            // $novaPessoa = new Pessoa();
            // $novaPessoa->pessoaJuridica = 0;
            // $novaPessoa->nomeCompleto = $request->name;
            // $novaPessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
            // $novaPessoa->ativo = 1;
            // $novaPessoa->save();

            //novo Usuário
            // $novoUsuario = new User();
            // $novoUsuario->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            // $novoUsuario->email = $request->email;
            // // $novoUsuario->telefone_celular = preg_replace('/[^0-9]/', '', $request->telefone_celular);
            // $novoUsuario->telefone_celular = $request->telefone_celular;
            // $novoUsuario->telefone_celular2 = preg_replace('/[^0-9]/', '', $request->telefone_celular2);
            // $novoUsuario->password = Hash::make($request->password);
            // $novoUsuario->tentativa_senha = 0;
            // $novoUsuario->bloqueadoPorTentativa = 0;
            // $novoUsuario->ativo = 1;
            // $novoUsuario->id_pessoa = $id_pessoa;
            // // $novoUsuario->id_tipo_perfil = 3;
            // $novoUsuario->confirmacao_email = 0;
            // $novoUsuario->envio_email_confirmacao = 0;
            // $novoUsuario->save();

            // adicionando tipo_perfil cliente ao usuário
            // $perfil_user = new PerfilUser();
            // $perfil_user->id_user = $novoUsuario->id;
            // $perfil_user->id_tipo_perfil = 2; //vereador
            // $perfil_user->cadastradoPorUsuario = $novoUsuario->id;
            // $perfil_user->ativo = 1;
            // $perfil_user->save();

            // adicionando perfil cliente aos perfis ativos do usuário
            // $permissao = new Permissao();
            // $permissao->id_user = $novoUsuario->id;
            // $permissao->id_perfil = 2;
            // $permissao->cadastradoPorUsuario = $novoUsuario->id;
            // $permissao->ativo = 1;
            // $permissao->save();

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
                'cliente' => $novoUsuario->pessoa->nomeCompleto,
                'link' => $link,
            ];

            EmailService::configuracaoEmail();

            EmailService::novoEmail($novoUsuario, $link);

            Mail::to($novoUsuario->email)->send(new ConfirmacaoEmail($details));

            return redirect()->route('login')->with('success', 'Cadastro realizado com sucesso! Encaminhado link de confirmação no seu email, cheque sua caixa de spam.');

        }
        // catch (ValidationException $e ) {
        //     $message = $e->errors();
        //     return redirect()->back()
        //         ->withErrors($message)
        //         ->withInput();
        // }
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
