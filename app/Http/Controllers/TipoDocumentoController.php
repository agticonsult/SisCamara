<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoDocumentoRequest;
use App\Http\Requests\TipoDocumentoUpdateRequest;
use App\Models\Departamento;
use App\Models\DepartamentoTramitacao;
use App\Models\TipoDocumento;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TipoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('TipoDocumento', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipoDocumentosAtivos = TipoDocumento::retornaTipoDocumentosAtivos();

            return view('configuracao.tipo-documento.index', compact('tipoDocumentosAtivos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
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
            if(Auth::user()->temPermissao('TipoDocumento', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $departamentos = Departamento::retornaDepartamentosAtivos();

            return view('configuracao.tipo-documento.create', compact('departamentos'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'create');
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
    public function store(TipoDocumentoRequest $request)
    {
        try{
            if(Auth::user()->temPermissao('TipoDocumento', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $validated = $request->validated();

            $tipoDocumento = TipoDocumento::create($validated + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            $departamentos = $validated['id_departamento'];
            for ($i = 0 ; $i < count($departamentos); $i++) {
                DepartamentoTramitacao::create([
                    'id_tipo_documento' => $tipoDocumento->id,
                    'id_departamento' => $departamentos[$i],
                    'ordem' => $i + 1,
                    'cadastradoPorUsuario' => Auth::user()->id
                ]);
            }
            Alert::toast('Tipo de documento cadastrado com sucesso.', 'success');
            return redirect()->route('configuracao.tipo_documento.index');

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            if(Auth::user()->temPermissao('TipoDocumento', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipoDocumento = TipoDocumento::retornaTipoDocumentoAtivo($id);
            if (!$tipoDocumento) {
                Alert::toast('Não foi encontrado tipo de documento!','error');
                return redirect()->back();
            }

            return view('configuracao.tipo-documento.edit', compact('tipoDocumento'));
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoDocumentoUpdateRequest $request, $id)
    {
        try{
            if(Auth::user()->temPermissao('TipoDocumento', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipoDocumento = TipoDocumento::retornaTipoDocumentoAtivo($id);
            if (!$tipoDocumento) {
                Alert::toast('Não foi encontrado tipo de documento!','error');
                return redirect()->back();
            }

            $tipoDocumento->update($request->validated());
            Alert::toast('Alteração realizado com sucesso!', 'success');
            return redirect()->route('configuracao.tipo_documento.index');
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function get($id)
    {
        try{
            if (Auth::user()->temPermissao('TipoDocumentoController', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipoDocumento = TipoDocumento::retornaTipoDocumentoAtivo($id);
            if (!$tipoDocumento) {
                Alert::toast('Tipo de documento inválido.','error');
                return redirect()->back();
            }
            return $this->success($tipoDocumento);
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'get');
            return $this->error('Erro', 'Contate o administrador do sistema', 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            /*if (Auth::user()->temPermissao('TipoDocumentoController', 'Exclusão') != 1) {
                Alert::toast('Acesso negado','error');
                return redirect()->back();
            }*/

            $tipo_documento = TipoDocumento::where('id', $id)->where('ativo', TipoDocumento::ATIVO)->first();
            $departamento_tramitacao = DepartamentoTramitacao::where('id_tipo_documento', $id)->where('ativo', DepartamentoTramitacao::ATIVO)->first();

            $tipo_documento->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => TipoDocumento::INATIVO
            ]);

            $departamento_tramitacao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => DepartamentoTramitacao::INATIVO
            ]);

            Alert::toast('Exclusão realizada com sucesso!', 'success');
            return redirect()->route('configuracao.tipo_documento.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'destroy');
            Alert::toast('Contate o administrador do sistema.','error');
            return redirect()->back();
        }
    }
}
