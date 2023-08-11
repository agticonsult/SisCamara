<?php

namespace App\Http\Controllers;

use App\Models\Agricultor;
use App\Models\ErrorLog;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Perfil;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\ValidadorCPFService;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('User', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            // $usuarios = User::all();
            $usuarios = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
                ->select(
                    'users.id', 'users.cpf', 'users.email', 'users.id_pessoa', 'users.ativo', 'users.tentativa_senha',
                    'users.bloqueadoPorTentativa', 'users.dataBloqueadoPorTentativa', 'users.created_at', 'users.inativadoPorUsuario',
                    'users.dataInativado', 'users.motivoInativado'
                )
                ->orderBy('users.ativo', 'asc')
                ->orderBy('pessoas.nomeCompleto', 'asc')
                ->get();
            return view('usuario.index', compact('usuarios'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if (Auth::user()->temPermissao('User', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $perfil_funcionarios = Perfil::where('id_tipo_perfil', '=', 2)->where('ativo', '=', 1)->get(); // perfil de funcionário
            $perfil_clientes = Perfil::where('id_tipo_perfil', '=', 3)->where('ativo', '=', 1)->get(); // perfil de cliente
            $perfil_adms = Perfil::where('id_tipo_perfil', '=', 1)->where('ativo', '=', 1)->get(); // perfil de adm

            return view('usuario.create', compact('perfil_funcionarios', 'perfil_clientes', 'perfil_adms'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(Request $request)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            //validação dos campos
            $input = [
                'nomeCompleto' => $request->nomeCompleto,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                'email' => $request->email,
                'password' => $request->password,
                'confirmacao' => $request->confirmacao,
                'tipo_perfil' => $request->tipo_perfil,
            ];
            $rules = [
                'nomeCompleto' => 'required|max:255',
                'cpf' => 'required|min:11|max:11',
                'email' => 'required|email',
                'dt_nascimento_fundacao' => 'required|max:10',
                'password' => 'required|min:6|max:35',
                'confirmacao' => 'required|min:6|max:35',
                'tipo_perfil' => 'required',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            //verifica se a confirmação de senha estão ok
            if($request->password != $request->confirmacao){
                return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
            }

            //varifica se já existe um email ativo cadaastrado no BD
            // $verifica_user = User::where('email', '=', $request->email)
            //     ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf))
            //     ->select('email', 'cpf')
            //     ->first();
            $verifica_user = User::where(function (Builder $query) use ($request) {
                return
                    $query->where('email', '=', $request->email)
                        ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf));
                    })
                ->select('id', 'email', 'cpf')
                ->first();


            //existe um email cadastrado?
            if($verifica_user){
                return redirect()->back()->with('erro', 'Já existe um usuário cadastrado com esse email e/ou CPF.')->withInput();
            }

            if (!ValidadorCPFService::ehValido($request->cpf)) {
                return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
            }

            $tipo_perfis = $request->tipo_perfil;
            foreach($tipo_perfis as $tipo_perfil){

                switch ($tipo_perfil) {
                    case '1':
                        $inputAdm = [
                            'id_perfil_adm' => $request->id_perfil_adm
                        ];
                        $rulesAdm = [
                            'id_perfil_adm' => 'required'
                        ];

                        $validarAdm = Validator::make($inputAdm, $rulesAdm);
                        $validarAdm->validate();
                        break;

                    case '2':
                        $inputFunc = [
                            'id_perfil_funcionario' => $request->id_perfil_funcionario
                        ];
                        $rulesFunc = [
                            'id_perfil_funcionario' => 'required'
                        ];

                        $validarFunc = Validator::make($inputFunc, $rulesFunc);
                        $validarFunc->validate();
                        break;

                    case '3':
                        $inputCliente = [
                            'id_perfil_cliente' => $request->id_perfil_cliente
                        ];
                        $rulesCliente = [
                            'id_perfil_cliente' => 'required'
                        ];

                        $validarCliente = Validator::make($inputCliente, $rulesCliente);
                        $validarCliente->validate();
                        break;

                    default:
                        return redirect()->back()->with('erro', 'Tipo de Perfil Inválido.')->withInput();
                        break;
                }
            }

            //nova Pessoa
            $novaPessoa = new Pessoa();
            $novaPessoa->nomeCompleto = $request->nomeCompleto;
            $novaPessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
            $novaPessoa->pessoaJuridica = 0;
            $novaPessoa->ativo = 1;
            $novaPessoa->save();

            //novo Usuário
            $novoUsuario = new User();
            $novoUsuario->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            $novoUsuario->email = $request->email;
            $novoUsuario->password = Hash::make($request->password);
            $novoUsuario->id_pessoa = $novaPessoa->id;
            $novoUsuario->tentativa_senha = 0;
            $novoUsuario->bloqueadoPorTentativa = 0;
            $novoUsuario->confirmacao_email = 1;
            $novoUsuario->envio_email_confirmacao = 0;
            $novoUsuario->ativo = 1;
            $novoUsuario->save();

            foreach($tipo_perfis as $tipo_perfil){

                switch ($tipo_perfil) {
                    case '1':
                        $perfil_user_adm = new PerfilUser();
                        $perfil_user_adm->id_user = $novoUsuario->id;
                        $perfil_user_adm->id_tipo_perfil = 1;
                        $perfil_user_adm->cadastradoPorUsuario = Auth::user()->id;
                        $perfil_user_adm->ativo = 1;
                        $perfil_user_adm->save();

                        $id_perfil_adms = $request->id_perfil_adm;
                        foreach ($id_perfil_adms as $ipa) {
                            $permissao_adm = new Permissao();
                            $permissao_adm->id_user = $novoUsuario->id;
                            $permissao_adm->id_perfil = $ipa;
                            $permissao_adm->cadastradoPorUsuario = Auth::user()->id;
                            $permissao_adm->ativo = 1;
                            $permissao_adm->save();
                        }
                        break;

                    case '2':
                        $perfil_user_func = new PerfilUser();
                        $perfil_user_func->id_user = $novoUsuario->id;
                        $perfil_user_func->id_tipo_perfil = 2;
                        $perfil_user_func->cadastradoPorUsuario = Auth::user()->id;
                        $perfil_user_func->ativo = 1;
                        $perfil_user_func->save();

                        $id_perfil_funcionarios = $request->id_perfil_funcionario;
                        foreach ($id_perfil_funcionarios as $ipf) {
                            $permissao_func = new Permissao();
                            $permissao_func->id_user = $novoUsuario->id;
                            $permissao_func->id_perfil = $ipf;
                            $permissao_func->cadastradoPorUsuario = Auth::user()->id;
                            $permissao_func->ativo = 1;
                            $permissao_func->save();
                        }
                        break;
                    case '3':
                        $perfil_user_cliente = new PerfilUser();
                        $perfil_user_cliente->id_user = $novoUsuario->id;
                        $perfil_user_cliente->id_tipo_perfil = 3;
                        $perfil_user_cliente->cadastradoPorUsuario = Auth::user()->id;
                        $perfil_user_cliente->ativo = 1;
                        $perfil_user_cliente->save();

                        $id_perfil_clientes = $request->id_perfil_cliente;
                        foreach ($id_perfil_clientes as $ipc) {
                            $permissao_cliente = new Permissao();
                            $permissao_cliente->id_user = $novoUsuario->id;
                            $permissao_cliente->id_perfil = $ipc;
                            $permissao_cliente->cadastradoPorUsuario = Auth::user()->id;
                            $permissao_cliente->ativo = 1;
                            $permissao_cliente->save();
                        }
                        break;
                }
            }

            return redirect()->route('usuario.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
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
                ->where('ativo', '=', 1)
                ->select('id', 'cpf', 'id_pessoa', 'email')
                ->first();

            if (!$usuario) {
                return redirect()->route('usuario.index')->with('erro', 'Não é possível alterar este usuário.');
            }

            // $municipios = Municipio::where('id_estado', '=', '16')->orderBy('descricao', 'asc')->where('ativo', '=', 1)->get();
            $tipo_perfis_ativos = $usuario->tipo_perfis_ativos;

            $ehAdm = 0;
            $ehFunc = 0;
            $ehClient = 0;
            $perfil_adms = array();
            $perfil_funcionarios = array();
            $perfil_clientes = array();
            $array_perfil_adms = array();
            $array_perfil_funcionarios = array();
            $array_perfil_clientes = array();

            foreach ($tipo_perfis_ativos as $tpa){

                switch ($tpa->id_tipo_perfil) {
                    case '1':
                        $ehAdm = 1; // se for adm
                        break;

                    case '2':
                        $ehFunc = 1; // se for funcionario, troca para perfil de funcionário
                        break;

                    case '3':
                        $ehClient = 1; // se for cliente, troca para perfil de cliente
                        break;

                    default:
                        # code...
                        break;
                }

            }

            if ($ehAdm == 0){
                $array_perfil_adms = Perfil::where('id_tipo_perfil', '=', 1)->where('ativo', '=', 1)->get();
            }
            else{
                $perfil_adms = Perfil::where('id_tipo_perfil', '=', 1)->where('ativo', '=', 1)->get();

                foreach($perfil_adms as $pa){
                    if ($usuario->temPerfil($pa->id) != 1){
                        array_push($array_perfil_adms, $pa);
                    }
                }
            }

            if ($ehFunc == 0){
                $array_perfil_funcionarios = Perfil::where('id_tipo_perfil', '=', 2)->where('ativo', '=', 1)->get();
            }
            else{
                $perfil_funcionarios = Perfil::where('id_tipo_perfil', '=', 2)->where('ativo', '=', 1)->get();

                foreach($perfil_funcionarios as $pf){
                    if ($usuario->temPerfil($pf->id) != 1){
                        array_push($array_perfil_funcionarios, $pf);
                    }
                }
            }

            if ($ehClient == 0){
                $array_perfil_clientes = Perfil::where('id_tipo_perfil', '=', 3)->where('ativo', '=', 1)->get();
            }
            else{
                $perfil_clientes = Perfil::where('id_tipo_perfil', '=', 3)->where('ativo', '=', 1)->get();

                foreach($perfil_clientes as $pc){
                    if ($usuario->temPerfil($pc->id) != 1){
                        array_push($array_perfil_clientes, $pc);
                    }
                }
            }

            return view('usuario.edit', compact(
                'usuario', 'array_perfil_adms', 'array_perfil_funcionarios', 'array_perfil_clientes', 'tipo_perfis_ativos',
                'ehAdm', 'ehFunc', 'ehClient'
            ));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
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
                'nomeCompleto' => $request->nomeCompleto,
                'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                'email' => $request->email
            ];
            $rules = [
                'nomeCompleto' => 'required|max:255',
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

            // if ($usuario->ehFuncionario() == 1){

            //     $input2 = [
            //         'lotacao' => $request->lotacao
            //     ];
            //     $rules2 = [
            //         'lotacao' => 'required|max:255'
            //     ];

            //     $validarLotacao = Validator::make($input2, $rules2);
            //     $validarLotacao->validate();

            //     $usuario->lotacao = $request->lotacao;
            // }

            $usuario->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            $usuario->email = $request->email;
            $usuario->ativo = 1;
            // dd($us);
            $usuario->save();

            // pessoa
            $pessoa = Pessoa::find($usuario->id_pessoa);
            $pessoa->nomeCompleto = $request->nomeCompleto;
            $pessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
            $pessoa->ativo = 1;
            $pessoa->save();

            $tipo_perfis = $request->tipo_perfil;
            foreach($tipo_perfis as $tipo_perfil){

                switch ($tipo_perfil) {
                    case '1':
                        if (isset($request->id_perfil_adm)){

                            $perfil_adms = $request->id_perfil_adm;
                            if (Count($perfil_adms) != 0){

                                if ($usuario->ehAdm() != 1){
                                    $perfil_user_adm = new PerfilUser();
                                    $perfil_user_adm->id_user = $id;
                                    $perfil_user_adm->id_tipo_perfil = 1;
                                    $perfil_user_adm->cadastradoPorUsuario = Auth::user()->id;
                                    $perfil_user_adm->ativo = 1;
                                    $perfil_user_adm->save();
                                }

                                foreach ($perfil_adms as $perfil_adm){

                                    // verificar se o perfil já foi adicionado para não repetir
                                    $tem_este_perfil = Permissao::where('id_user', '=', $id)
                                        ->where('id_perfil', '=', $perfil_adm)
                                        ->where('ativo', '=', 1)
                                        ->first();

                                    if (!$tem_este_perfil){

                                        $perf = Perfil::find($perfil_adm);

                                        if ($perf->id_tipo_perfil == 1){
                                            $permissao = new Permissao();
                                            $permissao->id_user = $id;
                                            $permissao->id_perfil = $perfil_adm;
                                            $permissao->cadastradoPorUsuario = Auth::user()->id;
                                            $permissao->ativo = 1;
                                            $permissao->save();
                                        }
                                    }
                                }
                            }
                        }
                        break;

                    case '2':
                        if (isset($request->id_perfil_funcionario)){

                            $perfil_funcs = $request->id_perfil_funcionario;
                            if (Count($perfil_funcs) != 0){

                                if ($usuario->ehFuncionario() != 1){
                                    $perfil_user_func = new PerfilUser();
                                    $perfil_user_func->id_user = $id;
                                    $perfil_user_func->id_tipo_perfil = 2;
                                    $perfil_user_func->cadastradoPorUsuario = Auth::user()->id;
                                    $perfil_user_func->ativo = 1;
                                    $perfil_user_func->save();
                                }

                                foreach ($perfil_funcs as $perfil_func){

                                    // verificar se o perfil já foi adicionado para não repetir
                                    $tem_este_perfil = Permissao::where('id_user', '=', $id)
                                        ->where('id_perfil', '=', $perfil_func)
                                        ->where('ativo', '=', 1)
                                        ->first();

                                    if (!$tem_este_perfil){

                                        $perf = Perfil::find($perfil_func);

                                        if ($perf->id_tipo_perfil == 2){
                                            $permissao = new Permissao();
                                            $permissao->id_user = $id;
                                            $permissao->id_perfil = $perfil_func;
                                            $permissao->cadastradoPorUsuario = Auth::user()->id;
                                            $permissao->ativo = 1;
                                            $permissao->save();
                                        }
                                    }
                                }
                            }
                        }
                        break;

                    case '3':
                        if (isset($request->id_perfil_cliente)){

                            $perfil_clientes = $request->id_perfil_cliente;
                            if (Count($perfil_clientes) != 0){

                                if ($usuario->ehCliente() != 1){
                                    $perfil_user_cliente = new PerfilUser();
                                    $perfil_user_cliente->id_user = $id;
                                    $perfil_user_cliente->id_tipo_perfil = 3;
                                    $perfil_user_cliente->cadastradoPorUsuario = Auth::user()->id;
                                    $perfil_user_cliente->ativo = 1;
                                    $perfil_user_cliente->save();
                                }

                                foreach ($perfil_clientes as $perfil_cliente){

                                    // verificar se o perfil já foi adicionado para não repetir
                                    $tem_este_perfil = Permissao::where('id_user', '=', $id)
                                        ->where('id_perfil', '=', $perfil_cliente)
                                        ->where('ativo', '=', 1)
                                        ->first();

                                    if (!$tem_este_perfil){

                                        $perf = Perfil::find($perfil_cliente);

                                        if ($perf->id_tipo_perfil == 3){
                                            $permissao = new Permissao();
                                            $permissao->id_user = $id;
                                            $permissao->id_perfil = $perfil_cliente;
                                            $permissao->cadastradoPorUsuario = Auth::user()->id;
                                            $permissao->ativo = 1;
                                            $permissao->save();
                                        }
                                    }
                                }
                            }
                        }
                    break;
                }
            }

            return redirect()->route('usuario.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    function desbloquear($id) {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $usuario = User::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$usuario){
                return redirect()->back()->with('erro', 'Não é possível desbloquear este usuário.')->withInput();
            }

            $usuario->tentativa_senha = 0;
            $usuario->bloqueadoPorTentativa = false;
            $usuario->dataBloqueadoPorTentativa = null;
            $usuario->save();


            return redirect()->route('usuario.index')->with('success', 'Usuário desbloqueado com sucesso.');

        } catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "desbloquear";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
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
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "destroy";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
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
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "restore";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
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

                //Quantos perfis do tipo sobrou?
                $id_user = $permissao->id_user;
                $id_tipo_perfil = $permissao->perfil->id_tipo_perfil;

                $count_perfis_ativos = Permissao::leftJoin('perfils', 'perfils.id', '=', 'permissaos.id_perfil')
                    ->where('permissaos.id_user', '=', $id_user)
                    ->where('perfils.id_tipo_perfil', '=', $id_tipo_perfil)
                    ->where('permissaos.ativo', '=', 1)
                    ->count();

                if ($count_perfis_ativos == 0){
                    $perfil_user = PerfilUser::where('id_user', '=', $id_user)
                        ->where('id_tipo_perfil', '=', $id_tipo_perfil)
                        ->where('ativo', '=', 1)
                        ->first();

                    $perfil_user->inativadoPorUsuario = auth()->user()->id;
                    $perfil_user->dataInativado = Carbon::now();
                    $perfil_user->motivoInativado = $request->motivo;
                    $perfil_user->ativo = 0;
                    $perfil_user->save();
                }

                $primeiroTipoPerfilAtivo = PerfilUser::where('id_user', '=', $id_user)->where('ativo', '=', 1)->first();
                if ($primeiroTipoPerfilAtivo){
                    $user = User::find($id_user);
                    $user->id_tipo_perfil = $primeiroTipoPerfilAtivo->id;
                    $user->save();
                }

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
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "UserController";
            $erro->funcao = "desativaPerfil";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
