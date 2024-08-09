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
use RealRashid\SweetAlert\Facades\Alert;

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
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
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
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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

            Alert::toast('Cadastro realizada com sucesso.','success');
            return redirect()->route('configuracao.gestao_administrativa.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'storeRecebimento');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function storeCadastroUsuario(Request $request)
    {
        try{
            if ($request->usuario_selecionados == null) {
                Alert::toast('Necessário selecionar 1 usuário.','warning');
                return redirect()->back();
            }

            foreach ($request->usuario_selecionados as $usuario) {
                $usuarioEncontrado = User::where('id', '=', $usuario)
                    ->where('cadastroAprovado', '=', User::USUARIO_REPROVADO)
                    ->where('ativo', '=', User::ATIVO)
                ->first();

                if ($usuarioEncontrado) {
                    $usuarioEncontrado->update([
                        'cadastroAprovado' => User::USUARIO_APROVADO,
                        'aprovadoPorUsuario' => Auth::user()->id,
                        'aprovadoEm' => Carbon::now()
                    ]);
                }
            }
            Alert::toast('Seleção realizada com sucesso.','success');
            return redirect()->route('aprovacao_cadastro_usuario.aprovacaoCadastroUsuario');
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'storeCadastroUsuario');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function aprovacaoCadastroUsuario()
    {
        try{
            $usuarios = User::Where('cadastroAprovado', '=', User::USUARIO_REPROVADO)
                ->where('confirmacao_email', '=', User::EMAIL_CONFIRMADO)
                ->where('ativo', '=', User::ATIVO)
            ->get();
            if (Auth::user()->permissaoAprovacaoUsuario()) {
                return view('usuario.aprovacao-cadastro.aprovacaoCadastro', compact('usuarios'));
            }

            return redirect()->route('home');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'aprovacaoCadastroUsuario');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try{
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $alterarGestaoAdm = GestaoAdministrativa::where('id', '=', $id)->where('ativo', '=', GestaoAdministrativa::ATIVO)->first();
            $departamentos = Departamento::where('ativo', '=', Departamento::ATIVO)->get();

            if (!$alterarGestaoAdm) {
                Alert::toast('Não foi encontrado Gestão Administrativa.','error');
                return redirect()->back();
            }

            return view('configuracao.gestao-adiministrativa.edit', compact('alterarGestaoAdm', 'departamentos'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(GestaoAdmRequest $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('GestaoAdministrativa', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $alterarGestaoAdm = GestaoAdministrativa::where('id', '=', $id)->where('ativo', '=', GestaoAdministrativa::ATIVO)->first();
            if (!$alterarGestaoAdm) {
                Alert::toast('Não foi encontrado Gestão Administrativa.','error');
                return redirect()->back();
            }

            $alterarGestaoAdm->update($request->validated());
            Alert::toast('Gestão Administrativa alterado com sucesso.','success');
            return redirect()->route('configuracao.gestao_administrativa.edit', $alterarGestaoAdm->id);
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'GestaoAdministrativaController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
