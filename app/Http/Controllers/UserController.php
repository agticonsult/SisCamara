<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\User;
use App\Services\ErrorLogService;
use App\Utils\PerfilUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('User', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $usuarios = User::retornaUsuariosAtivos();
            return view('usuario.index', compact('usuarios'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if (Auth::user()->temPermissao('User', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $perfils = Perfil::perfisAtivos();
            return view('usuario.create', compact('perfils'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'create');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function store(UserStoreRequest $request)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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
                'cadastroAprovado' => User::USUARIO_APROVADO
            ]);

            PerfilUtil::associarPerfis($request->id_perfil, $novoUsuario);
            // throw new Exception('forcando o erro');
            Alert::toast('Cadastro realizado com sucesso.', 'success');
            return redirect()->route('usuario.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('User', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $perfils = Perfil::perfisAtivos();
            $usuario = User::retornaUsuarioAtivo($id);
            if (!$usuario) {
                Alert::toast('Não é possível alterar este usuário.','error');
                return redirect()->route('usuario.index');
            }

            return view('usuario.edit', compact('usuario', 'perfils'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $usuario = User::retornaUsuarioAtivo($id);
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
            Alert::toast('Alteração realizado com sucesso.', 'success');
            return redirect()->back();

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function desbloquear($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $usuario = User::retornaUsuarioAtivo($id);
            if (!$usuario){
                Alert::toast('Não é possível desbloquear este usuário.','error');
                return redirect()->back();
            }
            $usuario->update([
                'tentativa_senha' => User::NAO_BLOQUEADO_TENTATIVA,
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'dataBloqueadoPorTentativa' => null,
            ]);
            Alert::toast('Usuário desbloqueado com sucesso.', 'success');
            return redirect()->route('usuario.index');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'desbloquear');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }

    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $usuario = User::retornaUsuarioAtivo($id);
            if (!$usuario){
                Alert::toast('Não é possível excluir este usuário.','error');
                return redirect()->back();
            }
            $usuario->update([
                'motivoInativado' => $motivo,
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'ativo' => User::INATIVO
            ]);

            $pessoa = Pessoa::find($usuario->id_pessoa);
            $pessoa->update([
                'motivoInativado' => $motivo,
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'ativo' => Pessoa::INATIVO
            ]);
            Alert::toast('Exclusão realizada com sucesso.', 'success');
            return redirect()->route('usuario.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function restore(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $usuario = User::retornaUsuarioInativo($id);
            if (!$usuario){
                Alert::toast('Usuário não encontrado!.','error');
                return redirect()->back();
            }

            $usuario->update([
                'inativadoPorUsuario' => null,
                'dataInativado' => null,
                'motivoInativado' => null,
                'ativo' => User::ATIVO
            ]);

            $pessoa = Pessoa::find($usuario->id_pessoa);
            $pessoa->update([
                'inativadoPorUsuario' => null,
                'dataInativado' => null,
                'motivoInativado' => null,
                'ativo' => Pessoa::ATIVO
            ]);
            Alert::toast('Recadastramento realizado com sucesso.', 'success');
            return redirect()->route('usuario.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'restore');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function desativaPerfil(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('User', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $usuario = User::retornaUsuarioAtivo($id);
            if (!$usuario) {
                Alert::toast('Houve erro ao desativar o perfil do usuário.','error');
                return redirect()->route('usuario.index');
            }

            $qtd_perfis = Permissao::where('id_user', '=', $usuario->id)->where('ativo', '=', Permissao::ATIVO)->count();

            if ($qtd_perfis > 1){

                $permissao = Permissao::where('id_user', '=', $usuario->id)->where('ativo', '=', Permissao::ATIVO)->first();
                if (!$permissao){
                    Alert::toast('Não é possível alterar este perfil.','error');
                    return redirect()->route('usuario.edit', $request->id_user_desativa);
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
                    Alert::toast('Perfil desativado com sucesso.', 'success');
                    return redirect()->route('home');
                }
                else{
                    Alert::toast('Perfil desativado com sucesso.', 'success');
                    return redirect()->back();
                }
            }
            else{
                Alert::toast('É necessário pelo menos 1 perfil ativo para o usuário.', 'success');
                return redirect()->route('usuario.edit', $request->id_user_desativa);
            }
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'UserController', 'desativaPerfil');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
