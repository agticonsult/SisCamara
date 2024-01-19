<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
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
            if (Auth::user()->temPermissao('User', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

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
            foreach($id_perfils as $id_perf) {
                $perfil = Perfil::where('id', '=', $id_perf)->where('ativo', '=', Perfil::ATIVO)->first();
                if ($perfil) {
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

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $usuario = User::where('id', '=', $id)->Where('ativo', '=', User::ATIVO)->first();
            $usuario->update($request->validated());

            $pessoa = Pessoa::find($usuario->id_pessoa);
            $pessoa->update($request->validated());

            $id_perfils = $request->id_perfil;
            foreach ($id_perfils as $id_perf){

                // verificar se o perfil já foi adicionado para não repetir
                $tem_este_perfil = Permissao::where('id_user', '=', $id)
                    ->where('id_perfil', '=', $id_perf)
                    ->where('ativo', '=', 1)
                    ->first();

                if (!$tem_este_perfil){
                    $perf = Perfil::where('id', '=', $id_perf)->where('ativo', '=', Perfil::ATIVO)->first();
                    if ($perf){
                        PerfilUser::create([
                            'id_user' => $usuario->id,
                            'id_tipo_perfil' => $id_perf,
                            'cadastradoPorUsuario' => $usuario->id,
                        ]);

                        Permissao::create([
                            'id_user' => $id,
                            'id_perfil' => $id_perf,
                            'cadastradoPorUsuario' => Auth::user()->id,
                        ]);
                    }
                }
            }
            return redirect()->back()->with('success', 'Alteração realizada com sucesso.');

        }
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
            $usuario->update([
                'tentativa_senha' => User::NAO_BLOQUEADO_TENTATIVA,
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'dataBloqueadoPorTentativa' => null,
            ]);

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
            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $usuario = User::where('id', '=', $id)->where('ativo', '=', User::ATIVO)->first();
            if (!$usuario){
                return redirect()->back()->with('erro', 'Não é possível excluir este usuário.')->withInput();
            }
            $usuario->update([
                'motivoInativado' => $motivo,
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'ativo' => User::INATIVO
            ]);

            return redirect()->route('usuario.index')->with('success', 'Exclusão realizada com sucesso.');
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

            $usuario = User::where('id', '=', $id)->where('ativo', '=', User::ATIVO)->first();
            if (!$usuario){
                return redirect()->back()->with('erro', 'Usuário não encontrado!.')->withInput();
            }

            $usuario->update([
                'inativadoPorUsuario' => null,
                'dataInativado' => null,
                'motivoInativado' => null,
                'ativo' => User::ATIVO
            ]);

            return redirect()->route('usuario.index')->with('success', 'Recadastramento realizado com sucesso.');
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

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $usuario = User::where('id', '=', $id)->where('ativo', '=', User::ATIVO)->first();
            if (!$usuario) {
                return redirect()->route('usuario.index')->with('erro', 'Houve erro ao desativar o perfil do usuário.');
            }

            $qtd_perfis = Permissao::where('id_user', '=', $usuario->id)->where('ativo', '=', Permissao::ATIVO)->count();

            if ($qtd_perfis > 1){

                $permissao = Permissao::where('id_user', '=', $usuario->id)->where('ativo', '=', Permissao::ATIVO)->first();
                if (!$permissao){
                    return redirect()->route('usuario.edit', $request->id_user_desativa)->with('erro', 'Não é possível alterar este perfil.')->withInput();
                }
                $perfilUser = PerfilUser::where('id_user', '=', $usuario->id)->where('ativo', '=', PerfilUser::ATIVO)->first();

                $permissao->update([
                   'inativadoPorUsuario' => Auth::user()->id,
                   'dataInativado' => Carbon::now(),
                   'motivoInativado' => $motivo,
                   'ativo' => Permissao::INATIVO
                ]);

                $perfilUser->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $motivo,
                    'ativo' => PerfilUser::INATIVO
                ]);

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
        catch(\Exception $ex){
            return $ex->getMessage();
            // ErrorLogService::salvar($ex->getMessage(), 'UserController', 'desativaPerfil');
            // return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
