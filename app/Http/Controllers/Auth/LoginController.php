<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use App\Models\FotoPerfil;
use App\Models\PerfilUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
{
    use ApiResponser;
    public function index() {
        return view('auth.login');
    }

    public function autenticacao(Request $request) {

        $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))
            ->where('ativo', '=', 1)
            ->select('id', 'cpf', 'email', 'bloqueadoPorTentativa', 'tentativa_senha', 'confirmacao_email')
            ->first();

        if($user){
            if($user->bloqueadoPorTentativa == 1){
                return redirect()->route('login')->with('erro', 'Usuário bloqueado por excesso de tentativas.')->withInput();
            }
            if($user->confirmacao_email == 0 || $user->confirmacao_email == false){
                return redirect()->back()->with('erro', 'Não foi confirmado o cadastro pelo link enviado no email.')->withInput();
            }

            $array = ['cpf' => preg_replace('/[^0-9]/', '', $request->cpf), 'password' => $request->password];

            // Se o acesso ao sistema exceder a 3 tentativas no campo senha, será bloqueado o usuário

            if (!Auth::attempt($array)) {
                $user->tentativa_senha++;

                if($user->tentativa_senha == 3){
                    $user->bloqueadoPorTentativa = 1;
                    $user->dataBloqueadoPorTentativa = Carbon::now();
                }
                $user->save();

                return redirect()->back()->with('erro', 'CPF de usuário ou Senha com dados incorretos')->withInput();
            };

            //Caso não seja excedido as tentativas de acesso, será zerado ou nulos os atributos abaixo e salva
            $user->tentativa_senha = 0;
            $user->bloqueadoPorTentativa = 0;
            $user->dataBloqueadoPorTentativa = null;
            $user->save();

            // $redirect = BUSCA DA URL;
            // if ($redirect == "acervos"){
            //     return redirect()->route(SUA ROTA ACERVOS CLIENTE);
            // }

            // $redirect = $_POST['redirect'];
            // dd(url());
            // dd($currentURL);
            // dd(Route::current());


            // if ($user->id_perfil == 3){
            //     return redirect('/acesso-externo/home');
            // }
            //    $url= request('redirect');
            //    dd( request('redirect'));

            $currentURL = url()->previous();
            $urlQuebrada = explode('?', $currentURL);
            if (Count($urlQuebrada) == 3){

                $txt = $urlQuebrada[2];
                if ($txt == 'eventos'){
                    if ($user->ehCliente() == 1){
                        return redirect()->route('acesso_externo.evento.index');
                    }

                    return redirect()->route('home')->with('warning', 'A inscrição nos eventos é destinada apenas aos clientes (agricultores).');
                }
                else{
                    if ($txt == 'acervo'){

                        if ($user->ehCliente() == 1){
                            return redirect()->route('acesso_externo.acervo.index');
                        }

                        return redirect()->route('home');
                    }
                }
            }

            // dd(
            //     $currentURL,
            //     $urlQuebrada,
            //     $txt
            // );



            // if ($_SERVER['HTTP_REFERER']=='http://localhost:8000/?link?acervo'|| $_SERVER['HTTP_REFERER']=='http://127.0.0.1:8000/?link?acervo') {

            //     return  redirect('/acervo/indexlog');
            // }
            // if ($_SERVER['HTTP_REFERER']=='http://localhost:8000/?link?eventos'|| $_SERVER['HTTP_REFERER']=='http://127.0.0.1:8000/?link?eventos') {

            //
            // }

            return redirect('/home');

        }
        else{
            return redirect()->route('login')->with('erro', 'CPF de usuário ou Senha com dados incorretos.')->withInput();
        }

    }

    public function autenticacaoAPI(Request $request)
    {
        try {
            // return $this->success('Ola');
            $input = [
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'password' => $request->password
            ];
            $rules = [
                'cpf' => 'required|min:11|max:11',
                'password' => 'required|min:6|max:35'
            ];
            $messages = [
                'cpf.required' => 'O CPF é obrigatório.',
                'cpf.min' => 'CPF: Mínimo 11 caracteres.',
                'cpf.max' => 'CPF: Máximo 11 caracteres.',

                'password.required' => 'A senha é obrigatória.',
                'password.max' => 'Senha: Máximo 35 caracteres.',
                'password.min' => 'Senha: mínimo 6 caracteres.'
            ];

            $validacao = Validator::make($input, $rules, $messages);
            $validacao->validate();

            $user = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))
                ->where('ativo', '=', 1)
                ->select('id', 'cpf', 'email', 'id_pessoa', 'bloqueadoPorTentativa', 'tentativa_senha', 'confirmacao_email')
                ->first();

            if($user){
                if($user->bloqueadoPorTentativa == 1){
                    return $this->error('Usuário bloqueado por excesso de tentativas.', 403);
                }
                if($user->confirmacao_email == 0 || $user->confirmacao_email == false){
                    return $this->error('Não foi confirmado o cadastro pelo link enviado no email.', 403);
                }

                $array = ['cpf' => preg_replace('/[^0-9]/', '', $request->cpf), 'password' => $request->password];

                if (!Auth::attempt($array)) {
                    $user->tentativa_senha++;

                    if($user->tentativa_senha == 3){
                        $user->bloqueadoPorTentativa = 1;
                        $user->dataBloqueadoPorTentativa = Carbon::now();
                    }
                    $user->save();


                    return $this->error('CPF de usuário ou Senha com dados incorretos.', 403);
                };

                $user->tentativa_senha = 0;
                $user->bloqueadoPorTentativa = 0;
                $user->dataBloqueadoPorTentativa = null;
                $user->save();

                // API destinada apenas aos clientes e funcionários
                // return $this->success($user->ehCliente());

                $ehCliente = 0;
                $ehFuncionario = 0;
                if ($user->ehCliente() == 1 || $user->ehFuncionario() == 1){

                    if ($user->ehCliente() == 1){
                        $ehCliente = 1;
                    }

                    if ($user->ehFuncionario() == 1){
                        $ehFuncionario = 1;
                    }

                }
                else{
                    return $this->error('Esta API é destinada apenas aos clientes ou funcionários.', 403);
                }
                // if ($user->ehCliente() != 1 && $user->ehFuncionario() != 1){
                //     return $this->error('Esta API é destinada apenas aos clientes ou funcionários.', 403);
                // }

                $token = $user->createToken('Teste')->plainTextToken;
                // return response()->json(['erro' => '1']);

                $foto_perfil_api = FotoPerfil::where('id_user', '=',$user->id)->where('ativo', '=', 1)->first();

                if(isset($foto_perfil_api)){
                    $path = asset('storage/foto-perfil/'.$foto_perfil_api->nome_hash);
                    // $path = storage_path('app/public/foto-perfil/'.$foto_perfil_api->nome_hash);
                    // $path = public_path() . '/foto-perfil/' . $foto_perfil_api->nome_hash;
                    // $path = 'https://www.bwengenhariams.com.br/Idr-Web/public/foto-perfil/' . $foto_perfil_api->nome_hash;
                }
                else{
                    // $path = 'Não há foto de perfil.';
                    $path = null;
                }

                return $this->success([
                    'token' => $token,
                    'id' => $user->id,
                    'name' => $user->pessoa->nome,
                    'email' => $user->email,
                    'foto' => $path,
                    'ehCliente' => $ehCliente,
                    'ehFuncionario' => $ehFuncionario
                ]);
                // return response()->json(['success' => $token]);
                // return redirect('/home');

            }
            else{
                return $this->error('CPF de usuário ou Senha com dados incorretos.', 403);
            }
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return $this->errorValidation($message, 403);
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->save();
            return $this->error($ex->getMessage(), 500);
        }

    }
}
