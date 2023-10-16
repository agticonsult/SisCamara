<?php

namespace App\Http\Controllers;

use App\Models\CargoEletivo;
use App\Models\ErrorLog;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\PleitoCargo;
use App\Models\PleitoEleitoral;
use App\Models\User;
use App\Models\Vereador;
use App\Services\ValidadorCPFService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VereadorController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Vereador', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereadores = Vereador::where('ativo', '=', 1)->get();

            return view('vereador.index', compact('vereadores'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
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
            if(Auth::user()->temPermissao('Vereador', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pleito_eleitorals = PleitoEleitoral::where('ativo', '=', 1)->get();
            $users = User::leftJoin('pessoas', 'pessoas.id', '=', 'users.id_pessoa')
                ->where('users.ativo', '=', 1)
                ->select('users.id', 'users.id_pessoa')
                ->orderBy('pessoas.nomeCompleto', 'asc')
                ->get();

            $usuarios = array();

            foreach ($users as $user) {

                if ($user->ehAgentePolitico() == 0){
                    array_push($usuarios, $user);
                }

            }


            return view('vereador.create', compact('pleito_eleitorals', 'usuarios'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
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
            if(Auth::user()->temPermissao('Vereador', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_pleito_eleitoral' => $request->id_pleito_eleitoral,
                // 'id_cargo_eletivo' => $request->id_cargo_eletivo,
                'dataInicioMandato' => $request->dataInicioMandato,
                'dataFimMandato' => $request->dataFimMandato,
                'selecionar_opcao' => $request->selecionar_opcao
            ];
            $rules = [
                'id_pleito_eleitoral' =>  'required|integer',
                // 'id_cargo_eletivo' => 'required|integer',
                'dataInicioMandato' => 'required|date',
                'dataFimMandato' => 'required|date',
                'selecionar_opcao' => 'required|max:255'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', 1)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', 1)
                ->first();
            if (!$pleito_cargo){
                return redirect()->back()->with('erro', 'Cargo eletivo inválido.')->withInput();
            }

            switch ($request->selecionar_opcao) {
                case '1':
                    $input_cad = [
                        'nomeCompleto' => $request->nomeCompleto,
                        'cpf' => preg_replace('/[^0-9]/', '', $request->cpf),
                        'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                        'email' => $request->email,
                        'password' => $request->password,
                        'confirmacao' => $request->confirmacao,
                        'telefone_celular' => preg_replace('/[^0-9]/', '', $request->telefone_celular),
                        'telefone_celular2' => preg_replace('/[^0-9]/', '', $request->telefone_celular2)
                    ];
                    $rules_cad = [
                        'nomeCompleto' => 'required|max:255',
                        'cpf' => 'required|min:11|max:11',
                        'email' => 'required|email',
                        'dt_nascimento_fundacao' => 'required|max:10',
                        'password' => 'required|min:6|max:35',
                        'confirmacao' => 'required|min:6|max:35',
                        'telefone_celular' => 'max:11',
                        'telefone_celular2' => 'max:11'
                    ];

                    $validar_cad = Validator::make($input_cad, $rules_cad);
                    $validar_cad->validate();

                    //verifica se a confirmação de senha estão ok
                    if($request->password != $request->confirmacao){
                        return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
                    }

                    //valida cpf
                    if(!ValidadorCPFService::ehValido($request->cpf)) {
                        return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
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

                    //Pessoa
                    $novaPessoa = new Pessoa();
                    $novaPessoa->pessoaJuridica = 0;
                    $novaPessoa->nomeCompleto = $request->nomeCompleto;
                    $novaPessoa->apelidoFantasia = $request->apelidoFantasia;
                    $novaPessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
                    $novaPessoa->cep = preg_replace('/[^0-9]/', '',$request->cep);
                    $novaPessoa->endereco = $request->endereco;
                    $novaPessoa->bairro = $request->bairro;
                    $novaPessoa->numero = $request->numero;
                    $novaPessoa->complemento = $request->complemento;
                    $novaPessoa->ponto_referencia = $request->ponto_referencia;
                    $novaPessoa->ativo = 1;
                    $novaPessoa->save();

                    //Usuário
                    $novoUsuario = new User();
                    $novoUsuario->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
                    $novoUsuario->email = $request->email;
                    $novoUsuario->telefone_celular = preg_replace('/[^0-9]/', '', $request->telefone_celular);
                    $novoUsuario->telefone_celular2 = preg_replace('/[^0-9]/', '', $request->telefone_celular2);
                    $novoUsuario->password = Hash::make($request->password);
                    $novoUsuario->bloqueadoPorTentativa = 0;
                    $novoUsuario->ativo = 1;
                    $novoUsuario->id_pessoa = $novaPessoa->id;
                    $novoUsuario->confirmacao_email = 1;
                    $novoUsuario->validado = 1;
                    $novoUsuario->validadoPorUsuario = Auth::user()->id;
                    $novoUsuario->validadoEm = Carbon::now();
                    $novoUsuario->save();

                    $permissao = new Permissao();
                    $permissao->id_user = $novoUsuario->id;
                    $permissao->id_perfil = 2;
                    $permissao->cadastradoPorUsuario = Auth::user()->id;
                    $permissao->ativo = 1;
                    $permissao->save();

                    $id_userzinho = $novoUsuario->id;
                    break;

                case '2':
                    $input_vinc = [
                        'id_usuario' => $request->id_usuario
                    ];
                    $rules_vinc = [
                        'id_usuario' => 'required|max:255'
                    ];

                    $validar_vinc = Validator::make($input_vinc, $rules_vinc);
                    $validar_vinc->validate();

                    $temUsuario = User::where('id', '=', $request->id_usuario)
                        ->where('ativo', '=', 1)
                        ->select('id')
                        ->first();

                    if (!$temUsuario){
                        return redirect()->back()->with('erro', 'Usuário não encontrado.')->withInput();
                    }

                    $id_userzinho = $temUsuario->id;
                    break;

                default:
                    return redirect()->back()->with('erro', 'Cadastrar usuário ou vincular a um usuário já existente? Informe no formulário!')->withInput();
                    break;
            }

            // Vereador
            $novoVereador = new Vereador();
            $novoVereador->dataInicioMandato = $request->dataInicioMandato;
            $novoVereador->dataFimMandato = $request->dataFimMandato;
            // $novoVereador->id_cargo_eletivo = $request->id_cargo_eletivo;
            $novoVereador->id_pleito_eleitoral = $request->id_pleito_eleitoral;
            $novoVereador->id_user = $id_userzinho;
            $novoVereador->cadastradoPorUsuario = Auth::user()->id;
            $novoVereador->ativo = 1;
            $novoVereador->save();

            return redirect()->route('vereador.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Vereador', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereador = Vereador::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$vereador){
                return redirect()->back()->with('erro', 'Vereador inválido.');
            }

            $pleito_eleitorals = PleitoEleitoral::where('ativo', '=', 1)->get();
            $pleito_cargos = $vereador->pleito_eleitoral->cargos_eletivos_ativos();
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
                ->orderBy('pessoas.nomeCompleto', 'asc')
                ->get();

            $usuarios = array();

            foreach ($users as $user) {

                if ($user->ehAgentePolitico() == 0){
                    array_push($usuarios, $user);
                }

            }

            return view('vereador.edit', compact('vereador', 'pleito_eleitorals', 'cargos_eletivos', 'usuarios'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
            $erro->funcao = "create";
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
            if(Auth::user()->temPermissao('Vereador', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $request->id,

                // Dados do vereador
                'id_pleito_eleitoral' => $request->id_pleito_eleitoral,
                // 'id_cargo_eletivo' => $request->id_cargo_eletivo,
                'dataInicioMandato' => $request->dataInicioMandato,
                'dataFimMandato' => $request->dataFimMandato,

                // Dados pessoais obrigatórios
                'nomeCompleto' => $request->nomeCompleto,
                'cpf' => preg_replace('/[^0-9]/', '',$request->cpf),
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                'email' => $request->email,

                // Dados pessoais não obrigatórios
                'apelidoFantasia' => $request->apelidoFantasia,
                'telefone_celular' => preg_replace('/[^0-9]/', '',$request->telefone_celular),
                'telefone_celular2' => preg_replace('/[^0-9]/', '',$request->telefone_celular2),
                'cep' => preg_replace('/[^0-9]/', '',$request->cep),
                'endereco' => $request->endereco,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento,
                'ponto_referencia' => $request->ponto_referencia,
            ];
            $rules = [
                'id' => 'required|integer',

                // Dados do vereador
                'id_pleito_eleitoral' => 'required|integer',
                // 'id_cargo_eletivo' => 'required|integer',
                'dataInicioMandato' => 'required|date',
                'dataFimMandato' => 'required|date',

                // Dados pessoais não obrigatórios
                'nomeCompleto' => 'required|max:255',
                'cpf' => 'required|min:11|max:11',
                'email' => 'required|email',
                'dt_nascimento_fundacao' => 'required|max:10',

                // Dados pessoais não obrigatórios
                'apelidoFantasia' => 'nullable|max:255',
                'telefone_celular' => 'nullable',
                'telefone_celular2' => 'nullable',
                'cep' => 'nullable',
                'endereco' => 'nullable|max:255',
                'numero' => 'nullable|max:255',
                'bairro' => 'nullable|max:255',
                'complemento' => 'nullable|max:255',
                'ponto_referencia' => 'nullable|max:255'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $vereador = Vereador::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$vereador){
                return redirect()->back()->with('erro', 'Vereador inválido.');
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', 1)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', 1)
                ->first();
            if (!$pleito_cargo){
                return redirect()->back()->with('erro', 'Cargo eletivo inválido.')->withInput();
            }

            //valida cpf
            if(!ValidadorCPFService::ehValido($request->cpf)) {
                return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
            }

            $verifica_user = User::where(function (Builder $query) use ($request) {
                return
                    $query->where('email', '=', $request->email)
                        ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf));
                    })
                ->select('id', 'email', 'cpf')
                ->first();

            //existe um email cadastrado?
            if($verifica_user){

                if ($verifica_user->id != $vereador->id_user) {
                    //caso exista um cpf ou e-mail cadastrado, retorne a mensagem abaixo
                    return redirect()->back()->with('erro', 'Já existe um usuário cadastrado com esse email e/ou CPF.')->withInput();
                }
                else{
                    //Pessoa
                    $pessoa = Pessoa::find($vereador->usuario->id_pessoa);
                    $pessoa->nomeCompleto = $request->nomeCompleto;
                    $pessoa->apelidoFantasia = $request->apelidoFantasia;
                    $pessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
                    $pessoa->cep = preg_replace('/[^0-9]/', '',$request->cep);
                    $pessoa->endereco = $request->endereco;
                    $pessoa->bairro = $request->bairro;
                    $pessoa->numero = $request->numero;
                    $pessoa->complemento = $request->complemento;
                    $pessoa->ponto_referencia = $request->ponto_referencia;
                    $pessoa->save();

                    //Usuário
                    $usuario = User::find($vereador->id_user);
                    $usuario->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
                    $usuario->email = $request->email;
                    $usuario->telefone_celular = preg_replace('/[^0-9]/', '', $request->telefone_celular);
                    $usuario->telefone_celular2 = preg_replace('/[^0-9]/', '', $request->telefone_celular2);
                    $usuario->save();
                }
            }

            // Vereador
            $vereador->dataInicioMandato = $request->dataInicioMandato;
            $vereador->dataFimMandato = $request->dataFimMandato;
            // $vereador->id_cargo_eletivo = $request->id_cargo_eletivo;
            $vereador->id_pleito_eleitoral = $request->id_pleito_eleitoral;
            $vereador->save();

            return redirect()->route('vereador.index')->with('success', 'Alteração realizada com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Vereador', 'Exclusão') != 1) {
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

            $pleito_eleitoral = PleitoEleitoral::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$pleito_eleitoral){
                return redirect()->back()->with('erro', 'Pleito eleitoral inválido.');
            }

            $pleito_eleitoral->inativadoPorUsuario = Auth::user()->id;
            $pleito_eleitoral->dataInativado = Carbon::now();
            $pleito_eleitoral->motivoInativado = $motivo;
            $pleito_eleitoral->ativo = 0;
            $pleito_eleitoral->save();

            foreach ($pleito_eleitoral->cargos_eletivos_ativos() as $pleito_cargo_ativo){
                $pleito_cargo_ativo->inativadoPorUsuario = Auth::user()->id;
                $pleito_cargo_ativo->dataInativado = Carbon::now();
                $pleito_cargo_ativo->motivoInativado = $motivo;
                $pleito_cargo_ativo->ativo = 0;
                $pleito_cargo_ativo->save();
            }

            return redirect()->route('processo-legislativo.pleito_eleitoral.index')->with('success', 'Exclusão realizada com sucesso.');
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
            $erro->controlador = "VereadorController";
            $erro->funcao = "destroy";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
