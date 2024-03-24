<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoDocumentoRequest;
use App\Http\Requests\TipoDocumentoUpdateRequest;
use App\Models\Departamento;
use App\Models\DepartamentoTramitacao;
use App\Models\Perfil;
use App\Models\TipoDocumento;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoDocumentosAtivos = TipoDocumento::retornaTipoDocumentosAtivos();

            return view('configuracao.tipo-documento.index', compact('tipoDocumentosAtivos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'index');
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
            if(Auth::user()->temPermissao('TipoDocumento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $departamentos = Departamento::retornaDepartamentosAtivos();

            return view('configuracao.tipo-documento.create', compact('departamentos'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
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
                return redirect()->back()->with('erro', 'Acesso negado.');
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

            return redirect()->route('configuracao.tipo_documento.index')->with('success', 'Tipo de documento cadastrado com sucesso.');

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoDocumento = TipoDocumento::retornaTipoDocumentoAtivo($id);
            if (!$tipoDocumento) {
                return redirect()->back()->with('erro', 'Não foi encontrado tipo de documento!');
            }

            return view('configuracao.tipo-documento.edit', compact('tipoDocumento'));
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
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
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoDocumento = TipoDocumento::retornaTipoDocumentoAtivo($id);
            if (!$tipoDocumento) {
                return redirect()->back()->with('erro', 'Não foi encontrado tipo de documento!');
            }

            $tipoDocumento->update($request->validated());

            return redirect()->route('configuracao.tipo_documento.index')->with('success', 'Alteração realizada com sucesso.');
        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoDocumentoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function get($id)
    {
        try{
            if (Auth::user()->temPermissao('TipoDocumentoController', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipoDocumento = TipoDocumento::retornaTipoDocumentoAtivo($id);
            if (!$tipoDocumento) {
                return redirect()->back()->with('erro', 'Tipo de documento inválido.');
            }

            return $this->success($tipoDocumento);

        }
        catch(\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'get');
            return $this->error('Erro', 'Contate o administrador do sistema', 500);
        }
    }
}
