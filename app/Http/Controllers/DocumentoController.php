<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\ErrorLog;
use App\Models\ModeloDocumento;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DocumentoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Documento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documentos = Documento::where('ativo', '=', Documento::ATIVO)->get();

            return view('documento.index', compact('documentos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Documento', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelos = ModeloDocumento::where('ativo', '=', Documento::ATIVO)->get();

            return view('documento.create', compact('modelos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function show($id){

    }

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Documento', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'nome' => $request->nome,
                'id_modelo' => $request->id_modelo,
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'nome' => 'required',
                'id_modelo' => 'required|integer',
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $modelo = ModeloDocumento::where('id', '=', $request->id_modelo)->where('ativo', '=', ModeloDocumento::ATIVO)->first();
            if (!$modelo){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $documento = new Documento();
            $documento->nome = $request->nome;
            $documento->id_modelo = $request->id_modelo;
            $documento->assunto = $request->assunto;
            $documento->conteudo = $request->conteudo;
            $documento->cadastradoPorUsuario = Auth::user()->id;
            $documento->ativo = 1;
            $documento->save();

            return redirect()->route('documento.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $documento = Documento::where('id', '=', $id)->where('ativo', '=', Documento::ATIVO)->first();
            if (!$documento){
                return redirect()->back()->with('erro', 'Documento inválido.');
            }

            $modelos = ModeloDocumento::where('ativo', '=', ModeloDocumento::ATIVO)->get();

            return view('documento.edit', compact('documento', 'modelos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Documento', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $id,
                'nome' => $request->nome,
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'id' => 'required|integer',
                'nome' => 'required',
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $documento = Documento::where('id', '=', $id)->where('ativo', '=', Documento::ATIVO)->first();
            if (!$documento){
                return redirect()->back()->with('erro', 'Documento inválido.');
            }

            $documento->nome = $request->nome;
            $documento->assunto = $request->assunto;
            $documento->conteudo = $request->conteudo;
            $documento->save();

            return redirect()->route('documento.index')->with('success', 'Alteração realizada com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Documento', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $documento = Documento::where('id', '=', $id)->where('ativo', '=', Documento::ATIVO)->first();
            if (!$documento){
                return redirect()->back()->with('erro', 'Documento inválido.');
            }

            $documento->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Documento::INATIVO
            ]);

            return redirect()->route('documento.index')->with('success', 'Exclusão realizada com sucesso.');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'DocumentoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
