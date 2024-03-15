<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartamentoDocumentoRequest;
use App\Http\Requests\StatusDepartamentoDocRequest;
use App\Models\Departamento;
use App\Models\DepartamentoDocumento;
use App\Models\DepartamentoTramitacao;
use App\Models\HistoricoMovimentacaoDoc;
use App\Models\StatusDepartamentoDocumento;
use App\Models\TipoDocumento;
use App\Models\TipoWorkflow;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartamentoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentoDocumentos = DepartamentoDocumento::retornaDocumentosDepAtivos();

            return view('departamento-documento.index', compact('departamentoDocumentos'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'index');
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
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoDocumentos = TipoDocumento::retornaTipoDocumentosAtivos();
            $statusDepDocs = StatusDepartamentoDocumento::retornaStatusAtivos();
            $tipo_workflows = TipoWorkflow::where('ativo', '=', TipoWorkflow::ATIVO)->get();

            return view('departamento-documento.create', compact('tipoDocumentos', 'statusDepDocs', 'tipo_workflows'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartamentoDocumentoRequest $request)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $timestamp = Carbon::now()->timestamp;
            $protocolo = $timestamp . '/' . rand(100000, 999999);

            $depDoc = DepartamentoDocumento::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id,
                'protocolo' => $protocolo
            ]);

            //registrando histórico de movimentação do documento
            HistoricoMovimentacaoDoc::create([
                'id_status' => DepartamentoDocumento::CRIACAO_DOC,
                'id_documento' => $depDoc->id,
                'id_usuario' => Auth::user()->id
            ]);

            return redirect()->route('departamento_documento.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DepartamentoDocumento  $departamentoDocumento
     * @return \Illuminate\Http\Response
     */
    public function show(DepartamentoDocumento $departamentoDocumento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DepartamentoDocumento  $departamentoDocumento
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentoDocumentoEdit = DepartamentoDocumento::retornaDocumentoDepAtivo($id);
            $historicoMovimentacao = HistoricoMovimentacaoDoc::retornaUltimoHistoricoMovStatusAtivo($departamentoDocumentoEdit->id);
            $todoHistoricoMovDocumento = HistoricoMovimentacaoDoc::retornaHistoricoMovAtivo($departamentoDocumentoEdit->id);
            $tipoDocumentos = TipoDocumento::retornaTipoDocumentosAtivos();
            $departamentoTramitacao = DepartamentoTramitacao::retornaDepartamentoTramitacoes($departamentoDocumentoEdit->id_tipo_documento);
            $proximoDep = DepartamentoTramitacao::retornaProximoDocumento($departamentoDocumentoEdit->id_tipo_documento);
            $statusDepDocs = StatusDepartamentoDocumento::retornaStatusAtivos();
            if (!$departamentoDocumentoEdit) {
                return redirect()->back()->with('erro', 'Documento inválido.');
            }

            return view('departamento-documento.edit', compact('departamentoDocumentoEdit', 'historicoMovimentacao', 'tipoDocumentos', 'departamentoTramitacao', 'statusDepDocs', 'proximoDep', 'todoHistoricoMovDocumento'));

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DepartamentoDocumento  $departamentoDocumento
     * @return \Illuminate\Http\Response
     */
    public function update(StatusDepartamentoDocRequest $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('DepartamentoDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentoDocumentoUpdate = DepartamentoDocumento::retornaDocumentoDepAtivo($id);
            $proximoDep = DepartamentoTramitacao::retornaProximoDocumento($departamentoDocumentoUpdate->id_tipo_documento);

            HistoricoMovimentacaoDoc::create($request->validated() + [
                'id_documento' => $departamentoDocumentoUpdate->id,
                'id_usuario' => Auth::user()->id,
                'id_departamento' => $proximoDep->id_departamento,
                'atualDepartamento' => HistoricoMovimentacaoDoc::ATUAL_DEPARTAMENTO
            ]);

            return redirect()->back()->with('success', 'Alteração realizado com sucesso.');

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DepartamentoDocumentoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DepartamentoDocumento  $departamentoDocumento
     * @return \Illuminate\Http\Response
     */
    public function destroy(DepartamentoDocumento $departamentoDocumento)
    {
        //
    }

}
