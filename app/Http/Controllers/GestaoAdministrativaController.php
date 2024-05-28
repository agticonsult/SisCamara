<?php

namespace App\Http\Controllers;

use App\Http\Requests\GestaoAdmRequest;
use App\Models\Departamento;
use App\Models\GestaoAdministrativa;
use App\Models\User;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GestaoAdministrativaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $gestoesAdm = GestaoAdministrativa::where('ativo', '=', GestaoAdministrativa::ATIVO)->get();
            $departamentos = Departamento::where('ativo', '=', Departamento::ATIVO)->get();
            $departamentosArray = array();

            foreach ($departamentos as $departamento) {
                if ($departamento->estaVinculadoGestaoAdm() == false) {
                    array_push($departamentosArray, $departamento);
                }
            }

            return view('configuracao.gestao-adiministrativa.index', compact('departamentos', 'departamentosArray', 'gestoesAdm'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GestaoAdmRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            if ($request->aprovacaoCadastro == 1 && $request->aprovacaoCadastro == 0) {
                return redirect()->back()->with('warning', 'Opção inválida de APROVAÇÃO DE CADASTRO.');
            }
            if ($request->recebimentoDocumento == 1 && $request->recebimentoDocumento == 0) {
                return redirect()->back()->with('warning', 'Opção inválida de RECEBIMENTO DE DOCUMENTO.');
            }

            GestaoAdministrativa::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('configuracao.gestao_administrativa.index')->with('success', 'Cadastro realizada com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'storeRecebimento');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function storeCadastroUsuario(Request $request)
    {
        try{
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            if ($request->usuario_selecionados == null) {
                return redirect()->back()->with('warning', 'Necessário selecionar 1 usuário.');
            }

            foreach ($request->usuario_selecionados as $usuario) {
                $usuarioEncontrado = User::where('id', '=', $usuario)->where('cadastroAprovado', '=', User::USUARIO_REPROVADO)->where('ativo', '=', User::ATIVO)->first();
                if ($usuarioEncontrado) {
                    $usuarioEncontrado->update([
                        'cadastroAprovado' => User::USUARIO_APROVADO,
                        'aprovadoPorUsuario' => Auth::user()->id,
                        'aprovadoEm' => Carbon::now()
                    ]);
                }
            }

            return redirect()->route('aprovacao_cadastro_usuario.aprovacaoCadastroUsuario')->with('success', 'Seleção realizada com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function aprovacaoCadastroUsuario()
    {
        try{
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $usuarios = User::Where('cadastroAprovado', '=', User::USUARIO_REPROVADO)->where('ativo', '=', User::ATIVO)->get();

            return view('configuracao.gestao-adiministrativa.aprovacao-cadastro.aprovacaoCadastro', compact('usuarios'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'aprovacaoCadastroUsuario');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try{
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $alterarGestaoAdm = GestaoAdministrativa::where('id', '=', $id)->where('ativo', '=', GestaoAdministrativa::ATIVO)->first();
            $departamentos = Departamento::where('ativo', '=', Departamento::ATIVO)->get();

            if (!$alterarGestaoAdm) {
                return redirect()->back()->with('erro', 'Não foi encontrado Gestão Administrativa.');
            }

            return view('configuracao.gestao-adiministrativa.edit', compact('alterarGestaoAdm', 'departamentos'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(GestaoAdmRequest $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $alterarGestaoAdm = GestaoAdministrativa::where('id', '=', $id)->where('ativo', '=', GestaoAdministrativa::ATIVO)->first();
            if (!$alterarGestaoAdm) {
                return redirect()->back()->with('erro', 'Não foi encontrado Gestão Administrativa.');
            }

            $alterarGestaoAdm->update($request->validated());

            return redirect()->route('configuracao.gestao_administrativa.edit', $alterarGestaoAdm->id)->with('success', 'Gestão Administrativa alterado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
