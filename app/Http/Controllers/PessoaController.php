<?php

namespace App\Http\Controllers;

use App\Models\Agricultor;
use App\Models\Auditoria;
use App\Models\CategoriaAgricultor;
use App\Models\Distrito;
use App\Models\Entidade;
use App\Models\ErrorLog;
use App\Models\Escolaridade;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\NatJurOrg;
use App\Models\Organizacao;
use App\Models\PerfilUser;
use App\Models\Permissao;
use App\Models\Pessoa;
use App\Models\RamoPj;
use App\Models\TipoOrganizacao;
use App\Models\User;
use App\Services\PermissaoService;
use App\Services\UserService;
use App\Services\ValidaCNPJService;
use App\Services\ValidadorCPFService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // $resposta = Auth::user()->temPermissaoAbrangencia('Pessoa', 'Listagem');
            // if ($resposta[0] != 1){
            //     return redirect()->back()->with('erro', 'Acesso negado.');
            // }
            if (Auth::user()->temPermissao('Pessoa', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pessoas = Pessoa::where('ativo', '=', 1)->with('usuario')->with('organizacao')->get();

            return view('cadastro.pessoa.index', compact('pessoas'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PessoaController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            if (Auth::user()->temPermissao('Pessoa', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $njs = NatJurOrg::where('ativo', '=', 1)->get();
            $tos = TipoOrganizacao::where('ativo', '=', 1)->get();
            $ramos = RamoPj::where('ativo', '=', 1)->get();
            $escolaridade = Escolaridade::where('ativo', '=', 1)->get();
            $cat_agricultor = CategoriaAgricultor::where('ativo', '=', 1)->get();
            $municipios = Municipio::where('id_estado', '=', '16')->orderBy('descricao', 'asc')->where('ativo', '=', 1)->get();


            return view('cadastro.pessoa.create', compact('njs', 'tos', 'ramos', 'escolaridade', 'cat_agricultor', 'municipios'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PessoaController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (Auth::user()->temPermissao('Pessoa', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                //dados pessoais
                'nome' => $request->nome,
                'apelidoFantasia' => $request->apelidoFantasia,
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,
                'pessoaJuridica' => $request->pessoaJuridica,

                //endereço
                'cep' => $request->cep,
                'endereco' => $request->endereco,
                'bairro' => $request->bairro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'ponto_referencia' => $request->ponto_referencia,
                'id_municipio' => $request->id_municipio,
            ];
            $rules = [

                //dados pessoais
                'nome' => 'required|max:255',
                'apelidoFantasia' => 'max:255',
                'dt_nascimento_fundacao' => 'required|max:255',
                'pessoaJuridica' => 'required|max:255',

                //endereço
                'cep' => 'max:255',
                'endereco' => 'max:255',
                'bairro' => 'max:255',
                'numero' => 'max:255',
                'complemento' => 'max:255',
                'ponto_referencia' => 'max:255',
                'id_municipio' => 'max:255',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            //Nova Pessoa
            $novaPessoa = new Pessoa();
            $novaPessoa->pessoaJuridica = $request->pessoaJuridica;
            $novaPessoa->nome = $request->nome;
            $novaPessoa->apelidoFantasia = $request->apelidoFantasia;
            $novaPessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
            $novaPessoa->cep = preg_replace('/[^0-9]/', '',$request->cep);
            $novaPessoa->endereco = $request->endereco;
            $novaPessoa->bairro = $request->bairro;
            $novaPessoa->numero = $request->numero;
            $novaPessoa->complemento = $request->complemento;
            $novaPessoa->ponto_referencia = $request->ponto_referencia;
            $novaPessoa->id_municipio = $request->id_municipio;
            $novaPessoa->cadastradoPorUsuario = Auth::user()->id;
            $novaPessoa->ativo = 1;
            $novaPessoa->save();

            switch ($request->pessoaJuridica) {

                //Pessoa não jurídica
                case "0":

                    $input = [
                        'cpf' => $request->cpf,
                        'titular' => $request->titular,
                        'cadPro' => $request->cadPro,
                        'email' => $request->email,
                        'password' => $request->password,
                        'confirmacao' => $request->confirmacao,
                    ];
                    $rules = [
                        'cpf' => 'required|min:11|max:255',
                        'titular' => 'required|max:255',
                        'cadPro' => 'required|max:255',
                        'email' => 'required|email',
                        'password' => 'required|max:255',
                        'confirmacao' => 'required|max:255',
                    ];

                    $validarUsuario = Validator::make($input, $rules);
                    $validarUsuario->validate();

                    //verifica se a confirmação de senha estão ok
                    if($request->password != $request->confirmacao){
                        return redirect()->back()->with('erro', 'Senhas não conferem.')->withInput();
                    }

                    //verifica se já existe um email ativo cadaastrado no BD
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

                    //Usuário
                    $novoUsuario = new User();

                    //validação CPF
                    if(!ValidadorCPFService::ehValido($request->cpf)){
                        return redirect()->back()->with('erro', 'CPF inválido.')->withInput();
                    }

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

                    //Agricultor
                    $novoAgricultor = new Agricultor();
                    $novoAgricultor->titular = $request->titular;
                    $novoAgricultor->cadPro = $request->cadPro;
                    $novoAgricultor->id_user = $novoUsuario->id;
                    $novoAgricultor->id_escolaridade = $request->id_escolaridade;
                    $novoAgricultor->id_categoria_agricultor = $request->id_categoria_agricultor;
                    $novoAgricultor->cadastradoPorUsuario = Auth::user()->id;
                    $novoAgricultor->validado = 1;
                    $novoAgricultor->validadoPorUsuario = Auth::user()->id;
                    $novoAgricultor->validadoEm = Carbon::now();
                    $novoAgricultor->ativo = 1;
                    $novoAgricultor->save();

                    // adicionando tipo_perfil cliente ao usuário
                    $perfil_user = new PerfilUser();
                    $perfil_user->id_user = $novoUsuario->id;
                    $perfil_user->id_tipo_perfil = 3;
                    $perfil_user->cadastradoPorUsuario = $novoUsuario->id;
                    $perfil_user->ativo = 1;
                    $perfil_user->save();

                    // adicionando perfil cliente aos perfis ativos do usuário
                    $permissao = new Permissao();
                    $permissao->id_user = $novoUsuario->id;
                    $permissao->id_perfil = 3;
                    $permissao->cadastradoPorUsuario = $novoUsuario->id;
                    $permissao->ativo = 1;
                    $permissao->save();

                break;

                //Pessoa jurídica
                case "1":

                    $input = [
                        //valiação dados pessoais
                        'cnpj' => $request->cnpj,
                        'nire' => $request->nire,
                        'apelidoFantasia' => $request->apelidoFantasia,
                        'inscricaoEstadual' => $request->inscricaoEstadual,
                        'id_naturezaJuridica' => $request->id_naturezaJuridica,
                        'id_tipoDeOrganizacao' => $request->id_tipoDeOrganizacao,
                        'id_ramoPJ' => $request->id_ramoPJ,
                    ];
                    $rules = [
                        // dados pessoais obrigatórios
                        'cnpj' => 'required|max:255',
                        'nire' => 'required|max:255',

                        // dados pessoais não obrigatórios
                        'apelidoFantasia' => 'max:255',
                        'inscricaoEstadual' => 'max:255',
                        'id_naturezaJuridica' => 'max:255',
                        'id_tipoDeOrganizacao' => 'max:255',
                        'id_ramoPJ' => 'max:255',
                    ];

                    $validarDados = Validator::make($input, $rules);
                    $validarDados->validate();

                    $cnpj = $request->cnpj;
                    //Realiza a validação CNPJ
                    $cnpjVerificar = new ValidaCNPJService($cnpj);
                    $valorFormatado = $cnpjVerificar->formata();
                    $valida = $cnpjVerificar->valida();

                    if($valida == false){
                        return redirect()->back()->with('erro', 'CNPJ inválido.')->withInput();
                    }

                    //verifica se já existe um CNPJ cadastrado, caso haja duplicidade do mesmo
                    $verifica_cnpj = Organizacao::where('cnpj', '=', preg_replace('/[^0-9]/', '', $valorFormatado))
                        ->select('cnpj')
                        ->first();

                    //caso haja CNPJ já cadastrado no BD, retorne com a seguinte mensagem abaixo
                    if($verifica_cnpj){
                        return redirect()->back()->with('erro', 'Já existe uma organização cadastrado com esse CNPJ.')->withInput();
                    }

                    //nova Organização
                    $novaOrganizacao = new Organizacao();
                    $novaOrganizacao->inscricaoEstadual = $request->inscricaoEstadual;
                    $novaOrganizacao->nire = $request->nire;
                    $novaOrganizacao->id_pj = $novaPessoa->id;
                    $novaOrganizacao->id_naturezaJuridica = $request->id_naturezaJuridica;
                    $novaOrganizacao->id_tipoDeOrganizacao = $request->id_tipoDeOrganizacao;
                    $novaOrganizacao->id_ramoPJ = $request->id_ramoPJ;
                    $novaOrganizacao->matrizFilial = $request->matrizFilial;
                    $novaOrganizacao->cnpj = preg_replace('/[^0-9]/', '', $valorFormatado);
                    $novaOrganizacao->cadastradoPorUsuario = Auth::user()->id;
                    $novaOrganizacao->ativo = 1;
                    $novaOrganizacao->save();

                    break;

                default:
                    return redirect()->back()->with('erro', 'Necessário selecionar a opção Pessoa Jurídica')->withInput();
                break;
            }

            return redirect()->route('pessoa.index')->with('success', 'Cadastro realizado com sucesso.');

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
            $erro->controlador = "PessoaController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Http\Response
     */
    public function show(Pessoa $pessoa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            if (Auth::user()->temPermissao('Pessoa', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $pessoa = Pessoa::where('id', '=', $id)->where('ativo', '=', 1)->with('usuario')->first();

            if (!$pessoa){
                return redirect()->route('pessoa.index')->with('erro', 'Não é possível alterar esta pessoa.');
            }

            $municipios = Municipio::where('id_estado', '=', 16)->orderBy('descricao', 'asc')->where('ativo', '=', 1)->get();
            $audits = Auditoria::where('auditable_type', '=', 'App\Models\Pessoa')->where('auditable_id', '=', $id)->get();

            return view('cadastro.pessoa.edit', compact('pessoa', 'municipios', 'audits'));

        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PessoaController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Pessoa', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                //dados pessoais
                'nome' => $request->nome,
                'apelidoFantasia' => $request->apelidoFantasia,
                'dt_nascimento_fundacao' => $request->dt_nascimento_fundacao,

                //endereço
                'cep' => $request->cep,
                'endereco' => $request->endereco,
                'bairro' => $request->bairro,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'ponto_referencia' => $request->ponto_referencia,
                'id_municipio' => $request->id_municipio,
            ];
            $rules = [

                //dados pessoais
                'nome' => 'required|max:255',
                'apelidoFantasia' => 'max:255',
                'dt_nascimento_fundacao' => 'max:255',

                //endereço
                'cep' => 'max:255',
                'endereco' => 'max:255',
                'bairro' => 'max:255',
                'numero' => 'max:255',
                'complemento' => 'max:255',
                'ponto_referencia' => 'max:255',
                'id_municipio' => 'max:255',
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $pessoa = Pessoa::find($id);
            $pessoa->nome = $request->nome;
            $pessoa->apelidoFantasia = $request->apelidoFantasia;
            $pessoa->dt_nascimento_fundacao = $request->dt_nascimento_fundacao;
            $pessoa->cep = preg_replace('/[^0-9]/', '',$request->cep);
            $pessoa->endereco = $request->endereco;
            $pessoa->bairro = $request->bairro;
            $pessoa->numero = $request->numero;
            $pessoa->complemento = $request->complemento;
            $pessoa->ponto_referencia = $request->ponto_referencia;
            $pessoa->id_municipio = $request->id_municipio;
            $pessoa->save();

            return redirect()->route('pessoa.index')->with('success', 'Cadastro alterado com sucesso.');

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
            $erro->controlador = "PessoaController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pessoa  $pessoa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pessoa $pessoa)
    {
        //
    }
}
