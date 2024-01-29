<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentePoliticoStoreRequest;
use App\Http\Requests\AgentePoliticoUpdateRequest;
use App\Http\Requests\AgentePoliticoVincularRequest;
use App\Models\AgentePolitico;
use App\Models\ErrorLog;
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
use App\Services\ValidadorCPFService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class AgentePoliticoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $agente_politicos = AgentePolitico::where('ativo', '=', AgentePolitico::ATIVO)->get();

            return view('agente-politico.index', compact('agente_politicos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            return view('agente-politico.create');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function novoAgentePolitico()
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
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
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function vincularUsuario(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
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
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(AgentePoliticoStoreRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', PleitoCargo::ATIVO)
                ->first();
            if (!$pleito_cargo){
                return redirect()->back()->with('erro', 'Cargo eletivo inválido.')->withInput();
            }

            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
            }

            $novaPessoa = Pessoa::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id,
                'pessoaJuridica' => Pessoa::NAO_PESSOA_JURIDICA
            ]);

            $novoUsuario = User::create($request->validated() + [
                'bloqueadoPorTentativa' => User::NAO_BLOQUEADO_TENTATIVA,
                'id_pessoa' => $novaPessoa->id,
                'confirmacao_email' => User::EMAIL_CONFIRMADO,
                'validado' => User::USUARIO_VALIDADO,
                'validadoPorUsuario' => Auth::user()->id,
                'validadoEm' => Carbon::now()
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

            //verifica se o arquivo é válido
            if($request->hasFile('fImage') && $request->file('fImage')->isValid()){

                $max_filesize = Filesize::where('id_tipo_filesize', '=', Filesize::FOTO_PERFIL)->where('ativo', '=', Filesize::ATIVO)->first();
                if ($max_filesize){
                    if ($max_filesize->mb != null){
                        if (is_int($max_filesize->mb)){
                            $mb = $max_filesize->mb;
                        }
                        else{
                            $mb = 2;
                        }
                    }
                    else{
                        $mb = 2;
                    }
                }
                else{
                    $mb = 2;
                }

                if (filesize($request->file('fImage')) <= 1048576 * $mb){
                    $nome_original = $request->fImage->getClientOriginalName();
                    $extensao = $request->fImage->extension();

                    //validação de extensão de imagens
                    if(
                        $extensao != 'jpg' &&
                        $extensao != 'jpeg' &&
                        $extensao != 'png'
                    ) {
                        return redirect()->back()->with('erro', 'Extensão de imagem inválida. Extensões permitidas .png, .jpg e .jpeg')->withInput();
                    }

                    $nome_hash = Uuid::uuid4();
                    $datahora = Carbon::now()->timestamp;
                    $nome_hash = $nome_hash . '-' . $datahora . '.' . $extensao;
                    //diretório onde estará as fotos de perfil
                    $upload = $request->fImage->storeAs('public/foto-perfil/', $nome_hash);
                    if(!$upload){
                        return redirect()->back()->with('erro', 'Ocorreu um erro ao salvar a foto de perfil.')->withInput();
                    }
                    else{
                        // $fotos = FotoPerfil::where('id_user', '=', $novoUsuario->id)->where('ativo', '=', FotoPerfil::ATIVO)->get();
                        // foreach ($fotos as $foto) {
                        //     $foto->update([
                        //         'ativo' => FotoPerfil::INATIVO,
                        //         'inativadoPorUsuario' => Auth::user()->id,
                        //         'dataInativado' => Carbon::now(),
                        //         'motivoInativado' => "Alteração de foto de perfil pelo usuário"
                        //     ]);
                        // }

                        FotoPerfil::create([
                            'nome_original' => $nome_original,
                            'nome_hash' => $nome_hash,
                            'id_user' => $novoUsuario->id,
                            'cadastradoPorUsuario' => Auth::user()->id
                        ]);
                    }
                }
                else{
                    return redirect()->back()->with('erro', 'Arquivo maior que ' . $mb . 'MB');
                }
            }

            AgentePolitico::create($request->validated() + [
                'id_legislatura' => $pleito_cargo->pleito_eleitoral->id_legislatura,
                'id_user' => $novoUsuario->id,
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('agente_politico.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
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
                return redirect()->back()->with('erro', 'Usuário não encontrado.')->withInput();
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', PleitoCargo::ATIVO)
                ->first();
            if (!$pleito_cargo){
                return redirect()->back()->with('erro', 'Cargo eletivo inválido.')->withInput();
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

            return redirect()->route('agente_politico.index')->with('success', 'Vinculação realizado com sucesso');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'storeVincular');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $agente_politico = AgentePolitico::where('id_user', '=', $id)->where('ativo', '=', AgentePolitico::ATIVO)->first();
            if (!$agente_politico){
                return redirect()->back()->with('erro', 'Agente Político inválido');
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
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(AgentePoliticoUpdateRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('AgentePolitico', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $agente_politico = AgentePolitico::where('id_user', '=', $id)->where('ativo', '=', AgentePolitico::ATIVO)->first();
            if (!$agente_politico){
                return redirect()->back()->with('erro', 'Agente Político inválido.');
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', PleitoCargo::ATIVO)
                ->first();
            if (!$pleito_cargo){
                return redirect()->back()->with('erro', 'Cargo eletivo inválido.')->withInput();
            }

            //Pessoa
            $pessoa = Pessoa::find($agente_politico->usuario->id_pessoa);
            $pessoa->update($request->validated());

            //Usuário
            $usuario = User::find($agente_politico->id_user);
            $usuario->update($request->validated());

            //verifica se o arquivo é válido
            if($request->hasFile('fImage') && $request->file('fImage')->isValid()) {
                $max_filesize = Filesize::where('id_tipo_filesize', '=', Filesize::FOTO_PERFIL)->where('ativo', '=', Filesize::ATIVO)->first();
                if ($max_filesize){
                    if ($max_filesize->mb != null){
                        if (is_int($max_filesize->mb)){
                            $mb = $max_filesize->mb;
                        }
                        else{
                            $mb = 2;
                        }
                    }
                    else{
                        $mb = 2;
                    }
                }
                else{
                    $mb = 2;
                }

                if (filesize($request->file('fImage')) <= 1048576 * $mb){
                    $nome_original = $request->fImage->getClientOriginalName();
                    $extensao = $request->fImage->extension();
                    //validação de extensão de imagens
                    if(
                        $extensao != 'jpg' &&
                        $extensao != 'jpeg' &&
                        $extensao != 'png'
                    ) {
                        return redirect()->back()->with('erro', 'Extensão de imagem inválida. Extensões permitidas .png, .jpg e .jpeg')->withInput();
                    }

                    $nome_hash = Uuid::uuid4();
                    $datahora = Carbon::now()->timestamp;
                    $nome_hash = $nome_hash . '-' . $datahora . '.' . $extensao;
                    //diretório onde estará as fotos de perfil
                    $upload = $request->fImage->storeAs('public/foto-perfil/', $nome_hash);
                    if(!$upload){
                        return redirect()->back()->with('erro', 'Ocorreu um erro ao salvar a foto de perfil.')->withInput();
                    }
                    else{
                        $fotos = FotoPerfil::where('id_user', '=', $agente_politico->id_user)->where('ativo', '=', FotoPerfil::ATIVO)->get();
                        foreach ($fotos as $foto) {
                            $foto->update([
                                'ativo' => FotoPerfil::INATIVO,
                                'inativadoPorUsuario' => Auth::user()->id,
                                'dataInativado' => Carbon::now(),
                                'motivoInativado' => "Alteração de foto de perfil pelo usuário"
                            ]);
                        }

                        FotoPerfil::create([
                            'nome_original' => $nome_original,
                            'nome_hash' => $nome_hash,
                            'id_user' => $agente_politico->id_user,
                            'cadastradoPorUsuario' => Auth::user()->id
                        ]);
                    }
                }
                else{
                    return redirect()->back()->with('erro', 'Arquivo maior que ' . $mb . 'MB');
                }
            }

            // Político
            $agente_politico->update($request->validated());

            return redirect()->route('agente_politico.edit', $agente_politico->id_user)->with('success', 'Alteração realizada com sucesso');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('AgentePolitico', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', PleitoEleitoral::ATIVO)->first();
            if (!$pleito_eleitoral){
                return redirect()->back()->with('erro', 'Pleito eleitoral inválido.');
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

            return redirect()->route('agente_politico.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AgentePoliticoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
