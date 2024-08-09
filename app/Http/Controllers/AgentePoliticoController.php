<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentePoliticoStoreRequest;
use App\Http\Requests\AgentePoliticoUpdateRequest;
use App\Http\Requests\AgentePoliticoVincularRequest;
use App\Models\AgentePolitico;
use App\Models\Filesize;
use App\Models\FotoPerfil;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\PleitoCargo;
use App\Models\PleitoEleitoral;
use App\Models\User;
use App\Services\ErrorLogService;
use App\Utils\UploadFotoUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class AgentePoliticoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $agente_politicos = AgentePolitico::where('ativo', '=', AgentePolitico::ATIVO)->get();

            return view('agente-politico.index', compact('agente_politicos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'index');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            return view('agente-politico.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'create');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function novoAgentePolitico()
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $filesize = Filesize::where('id_tipo_filesize', '=', Filesize::FOTO_PERFIL)->where('ativo', '=', Filesize::ATIVO)->first();
            $pleito_eleitorals = PleitoEleitoral::where('ativo', '=', PleitoEleitoral::ATIVO)->get();
            $users = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
                ->where('users.ativo', '=', 1)
                ->select('users.id', 'users.id_pessoa')
                ->orderBy('pessoas.nome', 'asc')
            ->get();

            $usuarios = array();
            foreach ($users as $user) {
                if ($user->ehAgentePolitico() == 0){
                    array_push($usuarios, $user);
                }
            }

            return view('agente-politico.novoUsuario', compact('pleito_eleitorals', 'usuarios', 'filesize'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'novoAgentePolitico');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function vincularUsuario(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $pleito_eleitorals = PleitoEleitoral::where('ativo', '=', PleitoEleitoral::ATIVO)->get();
            $users = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
                ->where('users.ativo', '=', 1)
                ->select('users.id', 'users.id_pessoa')
                ->orderBy('pessoas.nome', 'asc')
            ->get();

            $usuarios = array();
            foreach ($users as $user) {
                if ($user->usuarioInterno() == 1){
                    array_push($usuarios, $user);
                }
            }

            return view('agente-politico.vincularUsuario', compact('usuarios', 'pleito_eleitorals'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'vincularUsuario');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function store(AgentePoliticoStoreRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', PleitoCargo::ATIVO)
            ->first();
            if (!$pleito_cargo){
                Alert::toast('Cargo eletivo inválido.','error');
                return redirect()->back()->withInput();
            }

            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                Alert::toast('Senhas não conferem.','error');
                return redirect()->back()->withInput();
            }

            $novaPessoa = Pessoa::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id,
                'pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA
            ]);

            $novoUsuario = User::create($request->validated() + [
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'id_pessoa' => $novaPessoa->id,
                'confirmacao_email' => User::EMAIL_CONFIRMADO,
                'cadastroAprovado' => User::USUARIO_APROVADO,
                'aprovadoPorUsuario' => Auth::user()->id,
                'aprovadoEm' => Carbon::now()
            ]);

            PerfilUser::create([
                'id_user' => $novoUsuario->id,
                'id_tipo_perfil' => Perfil::USUARIO_POLITICO,
                'cadastradoPorUsuario' => $novoUsuario->id,
            ]);

            Permissao::create([
                'id_user' => $novoUsuario->id,
                'id_perfil' => Perfil::USUARIO_POLITICO,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            if ($request->fImage) {
                UploadFotoUtil::identificadorFileUpload($request, $novoUsuario);
            }

            AgentePolitico::create($request->validated() + [
                'id_legislatura' => $pleito_cargo->pleito_eleitoral->id_legislatura,
                'id_user' => $novoUsuario->id,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso!', 'success');
            return redirect()->route('agente_politico.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'store');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function storeVincular(AgentePoliticoVincularRequest $request)
    {
        try{
            $temUsuario = User::where('id', '=', $request->id_usuario)
                ->where('ativo', '=', User::ATIVO)
                ->select('id')
                ->first();
            if (!$temUsuario){
                Alert::toast('Usuário não encontrado.','error');
                return redirect()->back()->withInput();
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', PleitoCargo::ATIVO)
            ->first();
            if (!$pleito_cargo){
                Alert::toast('Cargo eletivo inválido.','error');
                return redirect()->back()->withInput();
            }

            $perfilUserAlterar = PerfilUser::where('id_user', '=', $temUsuario->id)->where('ativo', '=', PerfilUser::ATIVO)->first();
            if ($perfilUserAlterar) {
                $perfilUserAlterar->update([
                    'id_user' => $temUsuario->id,
                    'id_tipo_perfil' => Perfil::USUARIO_POLITICO
                ]);
            }

            $permissaoUserAlterar = Permissao::where('id_user', '=', $temUsuario->id)->where('ativo', '=', Permissao::ATIVO)->first();
            if ($permissaoUserAlterar) {
                $permissaoUserAlterar->update([
                    'id_user' => $temUsuario->id,
                    'id_perfil' => Perfil::USUARIO_POLITICO
                ]);
            }

            AgentePolitico::create($request->validated() + [
                'id_legislatura' => $pleito_cargo->pleito_eleitoral->id_legislatura,
                'id_user' => $temUsuario->id,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Vinculação realizado com sucesso!', 'success');
            return redirect()->route('agente_politico.index');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'storeVincular');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Alteração') != 1){
                Alert::toast('Acesso negado','error');
                return redirect()->back();
            }

            $agente_politico = AgentePolitico::where('id_user', '=', $id)->where('ativo', '=', AgentePolitico::ATIVO)->first();
            if (!$agente_politico){
                Alert::toast('Agente Político inválido.','error');
                return redirect()->back();
            }

            $foto_perfil = FotoPerfil::where('id_user', '=', $agente_politico->id_user)->where('ativo', '=', FotoPerfil::ATIVO)->first();
            $temFoto = FotoPerfil::NAO_TEM_FOTO;

            if ($foto_perfil){
                $existe = Storage::disk('public')->exists('foto-perfil/' . $foto_perfil->nome_hash);

                if ($existe){
                    $temFoto = FotoPerfil::TEM_FOTO;
                }
            }
            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', Filesize::ATIVO)->first();

            $pleito_eleitorals = PleitoEleitoral::where('ativo', '=', PleitoEleitoral::ATIVO)->get();
            $pleito_cargos = $agente_politico->pleito_eleitoral->cargos_eletivos_ativos();
            $cargos_eletivos = [];
            foreach ($pleito_cargos as $pleito_cargo) {
                $cargo_eletivo = [
                    'id' => $pleito_cargo->id_cargo_eletivo,
                    'descricao' => $pleito_cargo->cargo_eletivo->descricao
                ];
                array_push($cargos_eletivos, $cargo_eletivo);
            }
            $users = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
                ->where('users.ativo', '=', 1)
                ->select('users.id', 'users.id_pessoa')
                ->orderBy('pessoas.nome', 'asc')
                ->get();

            $usuarios = array();

            foreach ($users as $user) {
                if ($user->ehAgentePolitico() == 0){
                    array_push($usuarios, $user);
                }
            }

            return view('agente-politico.edit', compact('agente_politico', 'pleito_eleitorals', 'cargos_eletivos', 'usuarios', 'foto_perfil', 'temFoto', 'filesize'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'edit');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function update(AgentePoliticoUpdateRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Alteração') != 1){
                Alert::toast('Acesso negado','error');
                return redirect()->back();
            }

            $agente_politico = AgentePolitico::where('id_user', '=', $id)->where('ativo', '=', AgentePolitico::ATIVO)->first();
            if (!$agente_politico){
                Alert::toast('Agente Político inválido.','error');
                return redirect()->back();
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', PleitoCargo::ATIVO)
            ->first();
            if (!$pleito_cargo){
                Alert::toast('Cargo eletivo inválido.','error');
                return redirect()->back();
            }

            //Pessoa
            $pessoa = Pessoa::find($agente_politico->usuario->id_pessoa);
            $pessoa->update($request->validated());

            //Usuário
            $usuario = User::find($agente_politico->id_user);
            $usuario->update($request->validated());

            if ($request->fImage) {
                UploadFotoUtil::identificadorFileUpload($request, $usuario);
            }

            // Político
            $agente_politico->update($request->validated());

            Alert::toast('Alteração realizado com sucesso!', 'success');
            return redirect()->route('agente_politico.edit', $agente_politico->id_user);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'update');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('AgentePolitico', 'Exclusão') != 1) {
                Alert::toast('Acesso negado','error');
                return redirect()->back();
            }

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', PleitoEleitoral::ATIVO)->first();
            if (!$pleito_eleitoral){
                Alert::toast('Pleito eleitoral inválido.','error');
                return redirect()->back();
            }

            $pleito_eleitoral->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => PleitoEleitoral::INATIVO
            ]);

            foreach ($pleito_eleitoral->cargos_eletivos_ativos() as $pleito_cargo_ativo){
                $pleito_cargo_ativo->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $motivo,
                    'ativo' => PleitoEleitoral::INATIVO
                ]);
            }

            Alert::toast('Exclusão realizado com sucesso!', 'success');
            return redirect()->route('agente_politico.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'destroy');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }
}
