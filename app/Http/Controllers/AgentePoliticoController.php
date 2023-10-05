<?php

namespace App\Http\Controllers;

use App\Models\AgentePolitico;
use App\Models\ErrorLog;
use App\Models\Filesize;
use App\Models\FotoPerfil;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\PleitoCargo;
use App\Models\PleitoEleitoral;
use App\Models\User;
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

            $agente_politicos = AgentePolitico::where('ativo', '=', 1)->get();

            return view('agente-politico.index', compact('agente_politicos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AgentePoliticoController";
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
            if(Auth::user()->temPermissao('AgentePolitico', 'Listagem') != 1){
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


            return view('agente-politico.create', compact('pleito_eleitorals', 'usuarios'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AgentePoliticoController";
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
            if(Auth::user()->temPermissao('AgentePolitico', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id_pleito_eleitoral' => $request->id_pleito_eleitoral,
                'id_cargo_eletivo' => $request->id_cargo_eletivo,
                'dataInicioMandato' => $request->dataInicioMandato,
                'dataFimMandato' => $request->dataFimMandato,
                'selecionar_opcao' => $request->selecionar_opcao
            ];
            $rules = [
                'id_pleito_eleitoral' =>  'required|integer',
                'id_cargo_eletivo' => 'required|integer',
                'dataInicioMandato' => 'required|date',
                'dataFimMandato' => 'required|date',
                'selecionar_opcao' => 'required|max:255'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
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

                    if ($request->id_cargo_eletivo == 1){
                        $permissao = new Permissao();
                        $permissao->id_user = $novoUsuario->id;
                        $permissao->id_perfil = 2;
                        $permissao->cadastradoPorUsuario = Auth::user()->id;
                        $permissao->ativo = 1;
                        $permissao->save();
                    }

                    $id_userzinho = $novoUsuario->id;

                    //verifica se o arquivo é válido
                    if($request->hasFile('fImage') && $request->file('fImage')->isValid()){

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

                        // $nome_hash = Carbon::now()->timestamp;
                        // $nome_hash = $nome_hash.'.'.$extensao;
                        $nome_hash = Uuid::uuid4();
                        $datahora = Carbon::now()->timestamp;
                        $nome_hash = $nome_hash . '-' . $datahora . '.' . $extensao;
                        //diretório onde estará as fotos de perfil
                        $upload = $request->fImage->storeAs('public/foto-perfil/', $nome_hash);

                        if(!$upload){
                            return redirect()->back()->with('erro', 'Ocorreu um erro ao salvar a foto de perfil.')->withInput();
                        }
                        else{
                            $foto_perfil = new FotoPerfil();
                            $foto_perfil->nome_original = $nome_original;
                            $foto_perfil->nome_hash = $nome_hash;
                            $foto_perfil->id_user = $novoUsuario->id;
                            $foto_perfil->cadastradoPorUsuario = $novoUsuario->id;
                            $foto_perfil->ativo = 1;
                            $foto_perfil->save();
                        }

                        // $max_filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', 1)->first();
                        // if ($max_filesize){
                        //     if ($max_filesize->mb != null){
                        //         if (is_int($max_filesize->mb)){
                        //             $mb = $max_filesize->mb;
                        //         }
                        //         else{
                        //             $mb = 2;
                        //         }
                        //     }
                        //     else{
                        //         $mb = 2;
                        //     }
                        // }
                        // else{
                        //     $mb = 2;
                        // }

                        // if (filesize($request->file('fImage')) <= 1048576 * $mb){
                        //     $nome_original = $request->fImage->getClientOriginalName();
                        //     $extensao = $request->fImage->extension();

                        //     //validação de extensão de imagens
                        //     if(
                        //         $extensao != 'jpg' &&
                        //         $extensao != 'jpeg' &&
                        //         $extensao != 'png'
                        //     ) {
                        //         return redirect()->back()->with('erro', 'Extensão de imagem inválida. Extensões permitidas .png, .jpg e .jpeg')->withInput();
                        //     }

                        //     $nome_hash = Carbon::now()->timestamp;
                        //     $nome_hash = $nome_hash.'.'.$extensao;
                        //     //diretório onde estará as fotos de perfil
                        //     $upload = $request->fImage->storeAs('public/foto-perfil/', $nome_hash);
                        //     // $upload = $request->fImage->move('foto-perfil/', $nome_hash);

                        //     if(!$upload){
                        //         return redirect()->back()->with('erro', 'Ocorreu um erro ao salvar a foto de perfil.')->withInput();
                        //     }
                        //     else{
                        //         $foto_perfil = new FotoPerfil();
                        //         $foto_perfil->nome_original = $nome_original;
                        //         $foto_perfil->nome_hash = $nome_hash;
                        //         $foto_perfil->id_user = $novoUsuario->id;
                        //         $foto_perfil->cadastradoPorUsuario = $novoUsuario->id;
                        //         $foto_perfil->ativo = 1;
                        //         $foto_perfil->save();
                        //     }
                        // }
                        // else{
                        //     return redirect()->back()->with('erro', 'Arquivo maior que ' . $mb . 'MB');
                        // }
                    }
                    // else{
                    //     return redirect()->back()->with('erro', 'Selecione uma imagem.');
                    // }
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
            $agente_politico = new AgentePolitico();
            $agente_politico->dataInicioMandato = $request->dataInicioMandato;
            $agente_politico->dataFimMandato = $request->dataFimMandato;
            $agente_politico->id_legislatura = $pleito_cargo->pleito_eleitoral->id_legislatura;
            $agente_politico->id_cargo_eletivo = $request->id_cargo_eletivo;
            $agente_politico->id_pleito_eleitoral = $request->id_pleito_eleitoral;
            $agente_politico->id_user = $id_userzinho;
            $agente_politico->cadastradoPorUsuario = Auth::user()->id;
            $agente_politico->ativo = 1;
            $agente_politico->save();

            return redirect()->route('agente_politico.index')->with('success', 'Cadastro realizado com sucesso');
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
            $erro->controlador = "AgentePoliticoController";
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
            if(Auth::user()->temPermissao('AgentePolitico', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $agente_politico = AgentePolitico::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$agente_politico){
                return redirect()->back()->with('erro', 'Vereador inválido.');
            }

            $foto_perfil = FotoPerfil::where('id_user', '=', $agente_politico->id_user)->where('ativo', '=', 1)->first();
            $temFoto = 0;

            if ($foto_perfil){
                $existe = Storage::disk('public')->exists('foto-perfil/' . $foto_perfil->nome_hash);

                if ($existe){
                    $temFoto = 1;
                }
            }
            $filesize = Filesize::where('id_tipo_filesize', '=', 1)->where('ativo', '=', 1)->first();

            $pleito_eleitorals = PleitoEleitoral::where('ativo', '=', 1)->get();
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
                ->orderBy('pessoas.nomeCompleto', 'asc')
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
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AgentePoliticoController";
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
            if(Auth::user()->temPermissao('AgentePolitico', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $request->id,

                // Dados do agente político
                'id_pleito_eleitoral' => $request->id_pleito_eleitoral,
                'id_cargo_eletivo' => $request->id_cargo_eletivo,
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

                // Dados do agente político
                'id_pleito_eleitoral' => 'required|integer',
                'id_cargo_eletivo' => 'required|integer',
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

            $agente_politico = AgentePolitico::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$agente_politico){
                return redirect()->back()->with('erro', 'Agente Político inválido.');
            }

            $pleito_cargo = PleitoCargo::where('id_cargo_eletivo', '=', $request->id_cargo_eletivo)
                ->where('id_pleito_eleitoral', '=', $request->id_pleito_eleitoral)
                ->where('ativo', '=', 1)
                ->first();
            if (!$pleito_cargo){
                return redirect()->back()->with('erro', 'Cargo eletivo inválido.')->withInput();
            }

            //valida cpf
            // if(!ValidadorCPFService::ehValido($request->cpf)) {
            //     return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
            // }

            $verifica_user = User::where(function (Builder $query) use ($request) {
                return
                    $query->where('email', '=', $request->email)
                        ->orWhere('cpf', '=', preg_replace('/[^0-9]/', '', $request->cpf));
                    })
                ->select('id', 'email', 'cpf')
                ->first();

            if ($verifica_user->cpf != preg_replace('/[^0-9]/', '', $request->cpf)) {
                if(!ValidadorCPFService::ehValido($request->cpf)) {
                    return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
                }
            }

            //existe um email cadastrado?
            if($verifica_user){

                if ($verifica_user->id != $agente_politico->id_user) {
                    //caso exista um cpf ou e-mail cadastrado, retorne a mensagem abaixo
                    return redirect()->back()->with('erro', 'Já existe um usuário cadastrado com esse email e/ou CPF.')->withInput();
                }
                else{
                    //Pessoa
                    $pessoa = Pessoa::find($agente_politico->usuario->id_pessoa);
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
                    $usuario = User::find($agente_politico->id_user);
                    $usuario->cpf = preg_replace('/[^0-9]/', '', $request->cpf);
                    $usuario->email = $request->email;
                    $usuario->telefone_celular = preg_replace('/[^0-9]/', '', $request->telefone_celular);
                    $usuario->telefone_celular2 = preg_replace('/[^0-9]/', '', $request->telefone_celular2);
                    $usuario->save();
                }
            }

            // Vereador
            $agente_politico->dataInicioMandato = $request->dataInicioMandato;
            $agente_politico->dataFimMandato = $request->dataFimMandato;
            $agente_politico->id_legislatura = $pleito_cargo->pleito_eleitoral->id_legislatura;
            $agente_politico->id_cargo_eletivo = $request->id_cargo_eletivo;
            $agente_politico->id_pleito_eleitoral = $request->id_pleito_eleitoral;
            $agente_politico->save();

            return redirect()->route('agente_politico.index')->with('success', 'Alteração realizada com sucesso');
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
            $erro->controlador = "AgentePoliticoController";
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
            if (Auth::user()->temPermissao('AgentePolitico', 'Exclusão') != 1) {
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

            return redirect()->route('agente_politico.index')->with('success', 'Exclusão realizada com sucesso.');
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
            $erro->controlador = "AgentePoliticoController";
            $erro->funcao = "destroy";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
