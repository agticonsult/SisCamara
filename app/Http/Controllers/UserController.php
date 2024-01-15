<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\Agricultor;
use App\Models\ErrorLog;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\User;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\ValidadorCPFService;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('User', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $usuarios = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
                ->select(
                    'users.id', 'users.cpf', 'users.email', 'users.id_pessoa', 'users.ativo', 'users.tentativa_senha',
                    'users.bloqueadoPorTentativa', 'users.dataBloqueadoPorTentativa', 'users.created_at', 'users.inativadoPorUsuario',
                    'users.dataInativado', 'users.motivoInativado'
                )
                ->orderBy('users.ativo', 'asc')
                ->orderBy('pessoas.nome', 'asc')
                ->get();

            return view('usuario.index', compact('usuarios'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if (Auth::user()->temPermissao('User', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $perfils = Perfil::where('ativo', '=', Perfil::ATIVO)->get();

            return view('usuario.create', compact('perfils'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(UserStoreRequest $request)
    {
        try {
            //nova Pessoa
            $novaPessoa = Pessoa::create($request->validated() + [
                'pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            //novo Usuário
            $novoUsuario = User::create($request->validated() + [
                'id_pessoa' => $novaPessoa->id,
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'confirmacao_email' => User::EMAIL_CONFIRMADO,
            ]);

            $id_perfils = $request->id_perfil;
            foreach($id_perfils as $id_perf){

                $perfil = Perfil::where('id', '=', $id_perf)->where('ativo', '=', 1)->first();
                if ($perfil){

                    PerfilUser::create([
                        'id_user' => $novoUsuario->id,
                        'id_tipo_perfil' => $id_perf,
                        'cadastradoPorUsuario' => $novoUsuario->id,
                    ]);

                    Permissao::create([
                        'id_user' => $novoUsuario->id,
                        'id_perfil' => $id_perf,
                        'cadastradoPorUsuario' => $novoUsuario->id,
                    ]);
                }
            }
            // throw new Exception('forcando o erro');
            return redirect()->route('usuario.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $usuario = User::where('id', '=', $id)
                ->where('ativo', '=', User::ATIVO)
                ->select('id', 'cpf', 'id_pessoa', 'email')
                ->first();

            if (!$usuario) {
                return redirect()->route('usuario.index')->with('erro', 'Não é possível alterar este usuário.');
            }
            $perfils = Perfil::where('ativo', '=', Perfil::ATIVO)->get();

            return view('usuario.edit', compact('usuario', 'perfils'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            //validação dos campos
            $input = [
                'nome' => $request->nome,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                'email' => $request->email
            ];
            $rules = [
                'nome' => 'required|max:255',
                'cpf' => 'required|min:11|max:11',
                'email' => 'required|email',
                'dt_nascimento_fundacao' => 'required|max:10'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $usuario = User::find($id);

            if (!$usuario){
                return redirect()->back()->with('erro', 'Não é possível alterar este usuário.')->withInput();
            }

            // se cpf antigo é diferente do cpf novo
            if ($usuario->cpf != preg_replace('/[^0-9]/', '', $request->cpf)){ // mudou o cpf

                    //verificando se o cpf é valido
                if (!ValidadorCPFService::ehValido($request->cpf)) {
                    return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
                }

                // verificar se o novo cpf não está cadastrado no sistema
                $userCpf = User::where('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))->first();

                if ($userCpf){
                    return redirect()->back()->with('erro', 'Este CPF já está cadastrado no sistema.')->withInput();
                }
            }

            // se email antigo é diferente do email novo
            if ($usuario->email != $request->email){ // mudou o cpf

                // verificar se o novo email não está cadastrado no sistema
                $userEmail = User::where('email', '=', $request->email)->first();
                if ($userEmail){
                    return redirect()->back()->with('erro', 'Este e-mail já está cadastrado no sistema.')->withInput();
                }
            }

            $usuario->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            $usuario->email = $request->email;
            $usuario->ativo = 1;
            $usuario->save();

            // pessoa
            $pessoa = Pessoa::find($usuario->id_pessoa);
            $pessoa->nome = $request->nome;
            $pessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
            $pessoa->ativo = 1;
            $pessoa->save();

            $id_perfils = $request->id_perfil;
            foreach ($id_perfils as $id_perf){

                // verificar se o perfil já foi adicionado para não repetir
                $tem_este_perfil = Permissao::where('id_user', '=', $id)
                    ->where('id_perfil', '=', $id_perf)
                    ->where('ativo', '=', 1)
                    ->first();

                if (!$tem_este_perfil){
                    $perf = Perfil::find($id_perf);
                    if ($perf){
                        $permissao = new Permissao();
                        $permissao->id_user = $id;
                        $permissao->id_perfil = $id_perf;
                        $permissao->cadastradoPorUsuario = Auth::user()->id;
                        $permissao->ativo = 1;
                        $permissao->save();
                    }
                }
            }

            return redirect()->route('usuario.index')->with('success', 'Alteração realizada com sucesso.');

        }
        // catch (ValidationException $e ) {
        //     $message = $e->errors();
        //     return redirect()->back()
        //         ->withErrors($message)
        //         ->withInput();
        // }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function desbloquear($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $usuario = User::where('id', '=', $id)->where('ativo', '=', User::ATIVO)->first();

            if (!$usuario){
                return redirect()->back()->with('erro', 'Não é possível desbloquear este usuário.')->withInput();
            }

            $usuario->tentativa_senha = 0;
            $usuario->bloqueadoPorTentativa = false;
            $usuario->dataBloqueadoPorTentativa = null;
            $usuario->save();


            return redirect()->route('usuario.index')->with('success', 'Usuário desbloqueado com sucesso.');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'desbloquear');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }

    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'motivo' => $request->motivo
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $usuario = User::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$usuario){
                return redirect()->back()->with('erro', 'Não é possível excluir este usuário.')->withInput();
            }

            // if ($usuario->ehCliente() == 1){
            //     $agricultor = Agricultor::where('id_user', '=', $id)->where('ativo', '=', 1)->first();
            //     if ($agricultor){
            //         $agricultor->dataInativado = Carbon::now();
            //         $agricultor->inativadoPorUsuario = Auth::user()->id;
            //         $agricultor->motivoInativado = $motivo;
            //         $agricultor->ativo = 0;
            //         $agricultor->excluidoUserEAgricultor = 1;
            //         $agricultor->save();
            //     }
            // }

            $usuario->inativadoPorUsuario = Auth::user()->id;
            $usuario->dataInativado = Carbon::now();
            $usuario->motivoInativado = $motivo;
            $usuario->ativo = 0;
            $usuario->save();

            return redirect()->route('usuario.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function restore(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'motivo' => $request->motivo
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $usuario = User::find($id);

            if (!$usuario){
                return redirect()->back()->with('erro', 'Usuário inválido.')->withInput();
            }

            if ($usuario->ativo != 0){
                return redirect()->back()->with('erro', 'Este usuário está ativo.')->withInput();
            }

            // if ($usuario->ehCliente() == 1){
            //     $agricultor = Agricultor::where('id_user', '=', $id)->where('excluidoUserEAgricultor', '=', 1)->first();
            //     if ($agricultor){
            //         $agricultor->dataInativado = null;
            //         $agricultor->inativadoPorUsuario = null;
            //         $agricultor->motivoInativado = null;
            //         $agricultor->ativo = 1;
            //         $agricultor->excluidoUserEAgricultor = 0;
            //         $agricultor->save();
            //     }
            // }

            $usuario->inativadoPorUsuario = null;
            $usuario->dataInativado = null;
            $usuario->motivoInativado = null;
            $usuario->ativo = 1;
            $usuario->save();

            return redirect()->route('usuario.index')->with('success', 'Recadastramento realizado com sucesso.');
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'restore');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function desativaPerfil(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            //validação dos campos
            $input = [
                'id_user_desativa' => $request->id_user_desativa,
                'permissao_id' => $request->permissao_id
            ];
            $rules = [
                'id_user_desativa' => 'required',
                'permissao_id' => 'required'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $qtd_perfis = Permissao::where('id_user', '=', $request->id_user_desativa)->where('ativo', '=', 1)->count();

            if ($qtd_perfis > 1){

                $permissao = Permissao::where('id', '=', $request->permissao_id)->where('ativo', '=', 1)->first();

                if (!$permissao){
                    return redirect()->route('usuario.edit', $request->id_user_desativa)->with('erro', 'Não é possível alterar este perfil.')->withInput();
                }

                $permissao->inativadoPorUsuario = auth()->user()->id;
                $permissao->dataInativado = Carbon::now();
                $permissao->motivoInativado = $request->motivo;
                $permissao->ativo = 0;
                $permissao->save();

                $id_user = $permissao->id_user;
                if ($id_user == Auth::user()->id && Auth::user()->temPermissao('User', 'Alteração') != 1){
                    return redirect()->route('home')->with('success', 'Perfil desativado com sucesso.');
                }
                else{
                    return redirect()->back()->with('success', 'Perfil desativado com sucesso.');
                }

            }
            else{
                return redirect()->route('usuario.edit', $request->id_user_desativa)->with('erro', 'É necessário pelo menos 1 perfil ativo para o usuário.')->withInput();
            }
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'desativaPerfil');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
