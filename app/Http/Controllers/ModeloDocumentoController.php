<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\ModeloDocumento;
use App\Services\ErrorLogService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ModeloDocumentoController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelos = ModeloDocumento::retornaModelosAtivos();

            return view('documento.modelo.index', compact('modelos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            return view('documento.modelo.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $modelo_documento = new ModeloDocumento();
            $modelo_documento->assunto = $request->assunto;
            $modelo_documento->conteudo = $request->conteudo;
            $modelo_documento->cadastradoPorUsuario = Auth::user()->id;
            $modelo_documento->ativo = 1;
            $modelo_documento->save();

            return redirect()->route('documento.modelo.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelo_documento = ModeloDocumento::retornaModeloAtivo($id);
            if (!$modelo_documento){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            return view('documento.modelo.edit', compact('modelo_documento'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $id,
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'id' => 'required|integer',
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $modelo_documento = ModeloDocumento::retornaModeloAtivo($id);
            if (!$modelo_documento){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $modelo_documento->assunto = $request->assunto;
            $modelo_documento->conteudo = $request->conteudo;
            $modelo_documento->save();

            return redirect()->route('documento.modelo.index')->with('success', 'Alteração realizada com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('ModeloDocumento', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $modelo_documento = ModeloDocumento::retornaModeloAtivo($id);
            if (!$modelo_documento){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $modelo_documento->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => ModeloDocumento::ATIVO
            ]);

            return redirect()->route('documento.modelo.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function get($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelo_documento = ModeloDocumento::retornaModeloAtivo($id);
            if (!$modelo_documento){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            return $this->success($modelo_documento);
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'get');
            return $this->error('Erro, contate o administrador do sistema', 500);
        }
    }
}
