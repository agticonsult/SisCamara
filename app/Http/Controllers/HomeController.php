<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use App\Models\ErrorLog;
use App\Models\Estado;
use App\Models\Filesize;
use App\Models\FotoPerfil;
use App\Models\Municipio;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Pessoa;
use App\Models\TipoPerfil;
use App\Models\User;
use App\Services\ErrorLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\ValidadorCPFService;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        try{
            $user = User::where('id', '=', auth()->user()->id)
                ->select(
                    'id', 'cpf', 'email', 'telefone_celular', 'telefone_celular2',
                    'id_pessoa'
                )->first();

            $foto_perfil = FotoPerfil::where('id_user', '=', auth()->user()->id)->where('ativo', '=', User::ATIVO)->first();

            $temFoto = 0;

            if ($foto_perfil){
                $existe = Storage::disk('public')->exists('foto-perfil/'.$foto_perfil->nome_hash);
                // $existe = public_path('foto-perfil/'.$foto_perfil->nome_hash);
                if ($existe){
                    $temFoto = 1;
                }
            }

            // $municipio = Municipio::where('codIbge', '=', '4113700')->where('ativo', '=', 1)->first();dd($municipio);

            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', Filesize::ATIVO)->first();

            return view('home.home', compact('user', 'foto_perfil', 'temFoto', 'filesize'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'HomeController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            //validação dos campos
            $input = [
                //validação usuário
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'email' => $request->email,

                //valiação dados pessoais
                'nome' => $request->nome,
                'apelidoFantasia' => $request->apelidoFantasia,
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                'cep' => $request->cep,
                'endereco' => $request->endereco,
                'bairro' => $request->bairro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'ponto_referencia' => $request->ponto_referencia
            ];
            $rules = [
                //Usuário
                'cpf' => 'required|min:11|max:11',
                'email' => 'required|email',

                //Pessoa
                'nome' => 'required|max:255',
                'apelidoFantasia' => 'max:255',
                'dt_nascimento_fundacao' => 'required|date',
                'cep' => 'max:255',
                'endereco' => 'max:255',
                'bairro' => 'max:255',
                'numero' => 'max:255',
                'complemento' => 'max:255',
                'ponto_referencia' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            //Busca o usuário no BD
            $user = User::find($id);

            // se cpf antigo é diferente do cpf novo
            if ($user->cpf != preg_replace('/[^0-9]/', '', $request->cpf)){ // mudou o cpf

                // verificar se o novo cpf não está cadastrado no sistema
                $userCpf = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))->first();
                if ($userCpf){
                    return redirect()->back()->with('erro', 'Este CPF já está cadastrado no sistema.')->withInput();
                }

                //verificando se o cpf é valido
                if (!ValidadorCPFService::ehValido($request->cpf)) {
                    return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
                }

            }

            // se email antigo é diferente do email novo
            if ($user->email != $request->email){ // mudou o cpf

                // verificar se o novo email não está cadastrado no sistema
                $userEmail = User::where('email', '=', $request->email)->first();
                if ($userEmail){
                    return redirect()->back()->with('erro', 'Este EMAIL já está cadastrado no sistema.')->withInput();
                }
            }

            $user->email = $request->email;
            $user->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            $user->telefone_celular = preg_replace('/[^0-9]/', '', $request->telefone_celular);
            $user->telefone_celular2 = preg_replace('/[^0-9]/', '', $request->telefone_celular2);

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
                $pessoa->nome = $request->nome;
                $pessoa->apelidoFantasia = $request->apelidoFantasia;
                $pessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
                $pessoa->cep = preg_replace('/[^0-9]/', '',$request->cep);
                $pessoa->endereco = $request->endereco;
                $pessoa->bairro = $request->bairro;
                $pessoa->numero = $request->numero;
                $pessoa->complemento = $request->complemento;
                $pessoa->ponto_referencia = $request->ponto_referencia;
                $pessoa->save();
            }

            $user->ativo = 1;
            $user->save();

            return redirect()->route('home')->with('success', 'Cadastro alterado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'HomeController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
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
}
