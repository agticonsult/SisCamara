<?php

namespace App\Http\Controllers;

use App\Models\ModeloDocumento;
use App\Services\ErrorLogService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;

class ModeloDocumentoController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelos = ModeloDocumento::retornaModelosAtivos();

            return view('documento.modelo.index', compact('modelos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            return view('documento.modelo.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'create');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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
            Alert::toast('Cadastro realizado com sucesso','success');
            return redirect()->route('documento.modelo.index');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelo_documento = ModeloDocumento::retornaModeloAtivo($id);
            if (!$modelo_documento){
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            return view('documento.modelo.edit', compact('modelo_documento'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloDocumento', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
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
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            $modelo_documento->assunto = $request->assunto;
            $modelo_documento->conteudo = $request->conteudo;
            $modelo_documento->save();
            Alert::toast('Alteração realizada com sucesso','success');
            return redirect()->route('documento.modelo.index');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('ModeloDocumento', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $modelo_documento = ModeloDocumento::retornaModeloAtivo($id);
            if (!$modelo_documento){
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            $modelo_documento->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => ModeloDocumento::ATIVO
            ]);
            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('documento.modelo.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function get($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelo_documento = ModeloDocumento::retornaModeloAtivo($id);
            if (!$modelo_documento){
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            return $this->success($modelo_documento);
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'ModeloDocumentoController', 'get');
            return $this->error('Erro, contate o administrador do sistema', 500);
        }
    }
}
